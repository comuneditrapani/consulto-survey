<?php

// --- public interface -----------------------------------------

/**
  return the complete normalized survey, given its name
 */
function consulto_get_survey($survey_name, $lang = 'it') {
    $survey_id = consulto_get_survey_id($survey_name);
    $raw = consulto_get_raw_survey($survey_id);
    if (!$raw) return null;
    return consulto_normalize_survey($raw, $lang);
}

// --- custom post types ----------------------------------------

function consulto_register_cpt_survey() {
    register_post_type('consulto_survey', [
        'labels' => [
            'name' => 'Survey',
            'singular_name' => 'Survey',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-forms',
        'supports' => ['title', 'editor', 'page-attributes'],
        'has_archive' => false,
        'rewrite' => false,
    ]);
}

// --- database tables ------------------------------------------

function consulto_create_tables() {
    global $wpdb;

    $charset = $wpdb->get_charset_collate();

    $table_replies = $wpdb->prefix . 'consulto_replies';
    $table_answers = $wpdb->prefix . 'consulto_answers';

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $sql1 = "CREATE TABLE $table_replies (
        id INT NOT NULL AUTO_INCREMENT,
        created_at DATETIME NOT NULL,
        survey_id BIGINT UNSIGNED NOT NULL,
        PRIMARY KEY (id)
    ) $charset;";

    $sql2 = "CREATE TABLE $table_answers (
        id INT NOT NULL AUTO_INCREMENT,
        reply_id INT NOT NULL,
        question_id VARCHAR(50) NOT NULL,
        value TEXT,
        PRIMARY KEY (id),
        UNIQUE KEY uq_reply_question (reply_id, question_id)
    ) $charset;";

    dbDelta($sql1);
    dbDelta($sql2);
}

// --- i18n -----------------------------------------------------

function consulto_t($slug, $lang = 'it') {
    static $map = null;
    // l'inizializzazione statica con chiamata a funzione è
    // supportata solo da PHP 8.1 in poi.
    if ($map === null) {
        $map = consulto_get_i18n_map();
    }

    return $map[$slug][$lang]
    ?? $map[$slug]['en']
    ?? $slug;
}

// --- normalization --------------------------------------------

function consulto_normalize_entity($entity, $lang, $prefix) {
    $id = $entity['id'] ?? null;
    if (!$id) return null;

    $slug = $entity['slug'] ?? $prefix . '_' . $id . '_missing_slug';

    return [
        'id' => $id,
        'slug' => $slug,
        'label' => consulto_t($slug, $lang),
    ];
}

function consulto_normalize_survey($raw, $lang) {
    $normalize_option = function($option, $lang) {
        $base = consulto_normalize_entity($option, $lang, 'option');
        if (!$base) return null;

        return array_merge($base, [
            'value' => $option['value'] ?? $base['slug'],
        ]);
    };

    $normalize_question = function($question, $lang) use ($normalize_option) {
        $base = consulto_normalize_entity($question, $lang, 'question');
        if (!$base) return null;

        $normalized = array_merge($base, [
            'type' => $question['type'] ?? 'text',
        ]);

        if (isset($question['min'])) $normalized['min'] = $question['min'];
        if (isset($question['max'])) $normalized['max'] = $question['max'];

        // options
        if (!empty($question['options'])) {
            $normalized['options'] = array_values(array_filter(
                array_map($normalize_option, $question['options'], array_fill(0, count($question['options']), $lang))
            ));
        }

        return $normalized;
    };

    $normalize_section = function($section, $lang) use ($normalize_question) {
        $base = consulto_normalize_entity($section, $lang, 'section');
        if (!$base) return null;

        $normalized = array_merge($base, [
            'questions' => [],
        ]);
        if (!empty($section['questions'])) {
            $normalized['questions'] = array_values(array_filter(
                array_map($normalize_question, $section['questions'], array_fill(0, count($section['questions']), $lang))
            ));
        }

        return $normalized;
    };

    $survey = [
        'sections' => []
    ];

    if (!empty($raw['sections'])) {
        $survey['sections'] = array_values(array_filter(
            array_map($normalize_section, $raw['sections'], array_fill(0, count($raw['sections']), $lang))
        ));
    }

    return $survey;
}

// --- raw ------------------------------------------------------

/**
  return the survey id given its name
*/
function consulto_get_survey_id($name) {
    $posts = get_posts(array(
        'name' => $name,
        'post_type' => 'consulto_survey',
        'post_status' => 'publish',
        'posts_per_page' => 1));
    if(empty($posts)) {
        return null;
    }
    return $posts[0]->ID;
}

/**
  return complete raw survey data, given its id
*/
function consulto_get_raw_survey($survey_id) {
    /* i dati restituiti da questa funzione, sono da considerare
     * "crudi" e vanno normalizzati, vedi consulto_normalize_survey
     */
    $raw = get_post_meta($survey_id, '_consulto_survey_schema', true);
    $survey = json_decode($raw, true) ?: [];
    return $survey;
}

function consulto_get_i18n_map() {
    static $map = null;
    if ($map === null) {
        $raw = get_option('consulto_i18n_map', '{}');
        $map = json_decode($raw, true) ?: [];
    }
    return $map;
}

// --- rest api i18n --------------------------------------------

function consulto_rest_api_init () {
    consulto_register_autocomplete_routes();
    consulto_register_survey_routes();
    // consulto_register_whatever_routes();
}

function consulto_rebuild_i18n_flat_cache() {
    $raw = get_option('consulto_i18n_map', '{}');
    $map = json_decode($raw, true) ?: [];
    $i18n_flat = [];
    foreach ($map as $slug => $translations) {
        foreach ($translations as $lang => $label) {
            $i18n_flat[] = [
                'slug'  => $slug,
                'lang'  => $lang,
                'label' => $label,
            ];
        }
    }
    set_transient('consulto_i18n_flat', $i18n_flat, HOUR_IN_SECONDS);
}

function consulto_autocomplete_handler(WP_REST_Request $request) {
    $q = strtolower(trim($request->get_param('q') ?? ''));
    $lang = $request->get_param('lang') ?: 'it';
    if ($q === '') {
        return rest_ensure_response([]);
    }

    $i18n_flat = get_transient('consulto_i18n_flat');
    if ($i18n_flat === false) {
        $raw = get_option('consulto_i18n_map', '{}');
        $map = json_decode($raw, true) ?: [];
        $i18n_flat = [];
        foreach ($map as $slug => $translations) {
            foreach ($translations as $lang => $label) {
                $i18n_flat[] = [
                    'slug'  => $slug,
                    'lang'  => $lang,
                    'label' => $label,
                ];
            }
        }
        set_transient('consulto_i18n_flat', $i18n_flat, HOUR_IN_SECONDS);
    }

    $results = [];

    foreach ($i18n_flat as $item) {
        $slug  = strtolower($item['slug']);
        $label = strtolower($item['label']);

        $score = 0;
        $match = null;

        // 1. match slug
        if ($slug == $q) {
            $score = max($score, 100);
            $match = 'slug_equals';
        } elseif (str_ends_with($slug, $q)) {
            $score = max($score, 90);
            $match = 'slug_ends';
        } elseif (str_starts_with($slug, $q)) {
            $score = max($score, 70);
            $match = 'slug_starts';
        } elseif (strpos($slug, $q) !== false) {
            $score = max($score, 40);
            $match = 'slug_contains';
        }

        // 2. match label lingua corrente
        if ($item['lang'] === $lang) {
            if (str_starts_with($label, $q)) {
                $score = max($score, 80);
                $match = 'label_starts_lang';
            } elseif (strpos($label, $q) !== false) {
                $score = max($score, 60);
                $match = 'label_contains_lang';
            }
        } else {
            // altre lingue (fallback debole)
            if (strpos($label, $q) !== false) {
                $score = max($score, 50);
                $match = $match ?: 'label_contains_other_lang';
            }
        }
        if ($score > 0) {
            $results[] = [
                'slug'  => $item['slug'],
                'label' => $item['label'],
                'lang'  => $item['lang'],
                'match' => $match,
                'score' => $score,
            ];
        }
    }

    // deduplica per slug (teniamo il miglior score)
    $dedup = [];

    foreach ($results as $r) {
        $slug = $r['slug'];
        if (!isset($dedup[$slug]) || $r['score'] > $dedup[$slug]['score']) {
            $dedup[$slug] = $r;
        }
    }

    $results = array_values($dedup);

    // ordinamento per score
    usort($results, function ($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    return rest_ensure_response($results);
}

function consulto_register_autocomplete_routes() {
    register_rest_route('consulto/v1', '/autocomplete', [
        'methods'  => 'GET',
        'callback' => 'consulto_autocomplete_handler',
        'permission_callback' => '__return_true',
        // 'permission_callback' => function () {
        //     return current_user_can('edit_posts');
        // }
    ]);
}

function consulto_register_survey_routes() {
    register_rest_route('consulto/v1', '/survey/(?P<id>\d+)', [
        [
            'methods'  => 'GET',
            'callback' => function ($request) {
                $post_id = (int) $request['id'];
                $schema = get_post_meta($post_id, '_consulto_survey_schema', true);
                if (!$schema) {
                    return [
                        'title' => '',
                        'sections' => []
                    ];
                }
                return json_decode($schema, true);
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ],
        [
            'methods'  => 'POST',
            'callback' => function ($request) {
                $post_id = (int) $request['id'];
                $data = $request->get_json_params();
                update_post_meta(
                    $post_id,
                    '_consulto_survey_schema',
                    wp_json_encode($data)
                );
                return ['ok' => true];
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]
    ]);
}
