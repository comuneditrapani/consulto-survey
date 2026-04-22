<?php

// --- public interface -----------------------------------------

function consulto_get_survey($id, $lang = 'it') {
    $raw = consulto_get_survey_definition($id);

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

function consulto_get_survey_definition($id) {
    /* per il momento hard coded, solo poche risposte pre-programmate;
     * a regime, i dati verranno dal DB.
     *
     * i dati restituiti da questa funzione, o recuperati dalla base
     * dati, sono da considerare "crudi" e vanno normalizzati,
     */
    static $surveys = [
        '5' => [
            'id' => 1,
            'sections' => [
                [
                    'id' => 2, 'slug' => 'section_profile',
                    'questions' => [
                        [
                            'id' => 3, 'slug' => 'question_profile_type',
                            'type' => 'single',
                            'options' => [
                                ['value' => 'resident', 'id'=>4, 'slug' => 'option_resident'],
                                ['value' => 'resident_partial', 'id'=>5, 'slug' => 'option_resident_partial'],
                                ['value' => 'visitor', 'id'=>6, 'slug' => 'option_visitor'],
                                ['value' => 'business', 'id'=>7, 'slug' => 'option_business'],
                                ['value' => 'other', 'id'=>8, 'slug' => 'option_other'],
                            ]
                        ],
                        [
                            'id'=>9, 'slug' => 'question_profile_age',
                            'type' => 'single',
                            'options' => [
                                ['value' => 'under_18', 'id'=>10, 'slug' => 'option_under_18'],
                                ['value' => '18_29', 'id'=>11, 'slug' => 'option_18_29'],
                                ['value' => '30_44', 'id'=>12, 'slug' => 'option_30_44'],
                                ['value' => '45_65', 'id'=>13, 'slug' => 'option_45_65'],
                                ['value' => 'over_65', 'id'=>14, 'slug' => 'option_over_65'],
                            ]
                        ],
                    ]
                ],
                [
                    'id'=>15, 'slug' => 'section_usage',
                    'questions' => [
                        [
                            'id'=>16, 'slug' => 'question_usage_frequency',
                            'type' => 'single',
                            'options' => [
                                ['value' => 'daily', 'id'=>17, 'slug' => 'option_daily'],
                                ['value' => 'weekly', 'id'=>18, 'slug' => 'option_weekly'],
                            ]
                        ],
                        [
                            'id'=>19, 'slug' => 'question_usage_means',
                            'type' => 'single',
                            'options' => [
                                ['value' => 'car', 'id'=>20, 'slug' => 'option_car'],
                                ['value' => 'foot', 'id'=>21, 'slug' => 'option_foot'],
                                ['value' => 'bus', 'id'=>22, 'slug' => 'option_bus'],
                                ['value' => 'bike', 'id'=>23, 'slug' => 'option_bike'],
                            ]
                        ],
                    ]
                ],
                [
                    'id'=>24, 'slug' => 'section_perception',
                    'questions' => [
                        [
                            'id'=>25, 'slug' => 'question_perception_spaces',
                            'type' => 'scale',
                            'min' => 1,
                            'max' => 5
                        ],
                        [
                            'id'=>26, 'slug' => 'question_perception_mobility',
                            'type' => 'scale',
                            'min' => 1,
                            'max' => 5
                        ],
                    ]
                ],
                [
                    'id'=>27, 'slug' => 'section_priorities',
                    'questions' => [
                        [
                            'id'=>28, 'slug' => 'question_priorities_ordered',
                            'type' => 'ranking',
                            'options' => [
                                ['value' => 'car', 'id'=>29, 'slug' => 'option_priority_car'],
                                ['value' => 'pedestrian', 'id'=>30, 'slug' => 'option_priority_pedestrian'],
                                ['value' => 'cycling', 'id'=>31, 'slug' => 'option_priority_cycling'],
                                ['value' => 'green', 'id'=>32, 'slug' => 'option_priority_green'],
                                ['value' => 'family', 'id'=>33, 'slug' => 'option_priority_family'],
                                ['value' => 'buses', 'id'=>34, 'slug' => 'option_priority_buses'],
                                ['value' => 'drainage', 'id'=>35, 'slug' => 'option_priority_drainage'],
                                ['value' => 'aesthetic', 'id'=>36, 'slug' => 'option_priority_aesthetic'],
                            ]
                        ],
                        [
                            'id'=>37, 'slug' => 'question_priorities_check',
                            'type' => 'single',
                            'options' => [
                                ['value' => 'favour', 'id'=>38, 'slug' => 'option_favour'],
                                ['value' => 'neutral', 'id'=>39, 'slug' => 'option_neutral'],
                                ['value' => 'against', 'id'=>40, 'slug' => 'option_against'],
                            ]
                        ],
                    ]
                ],
                [
                    'id'=>41, 'slug' => 'section_hints',
                    'questions' => [
                        [
                            'id'=>42, 'slug' => 'question_hints_top',
                            'type' => 'text',
                        ],
                        [
                            'id'=>43, 'slug' => 'question_hints_tip',
                            'type' => 'text',
                        ],
                    ]
                ]
            ]
        ],
        '42' => [
            'sections' => [
                [
                    'id'=>44, 'slug' => 'section_projects',
                    'questions' => [
                        [
                            'id'=>45, 'slug' => 'question_projects_selected',
                            'type' => 'ranking-partial',
                            'options' => [
                                ['value' => 'urban_mobility_plan', 'id'=>46, 'slug' => 'option_project_urban_mobility_plan'],
                                ['value' => 'pedestrian_zone_expansion', 'id'=>47, 'slug' => 'option_project_pedestrian_zone_expansion'],
                                ['value' => 'bicycle_network_upgrade', 'id'=>48, 'slug' => 'option_project_bicycle_network_upgrade'],
                                ['value' => 'public_transport_study', 'id'=>49, 'slug' => 'option_project_public_transport_study'],
                                ['value' => 'smart_traffic_system', 'id'=>50, 'slug' => 'option_project_smart_traffic_system'],
                                ['value' => 'parking_reform', 'id'=>51, 'slug' => 'option_project_parking_reform'],
                                ['value' => 'low_emission_zone', 'id'=>52, 'slug' => 'option_project_low_emission_zone'],
                                ['value' => 'center_accessibility', 'id'=>53, 'slug' => 'option_project_center_accessibility'],
                                ['value' => 'level_crossing_improvement','id'=>54, 'slug' => 'option_project_level_crossing_improvement'],
                                ['value' => 'waterfront_redevelopment', 'id'=>55, 'slug' => 'option_project_waterfront_redevelopment'],
                                ['value' => 'park_renovation', 'id'=>56, 'slug' => 'option_project_park_renovation'],
                                ['value' => 'urban_tree_planting', 'id'=>57, 'slug' => 'option_project_urban_tree_planting'],
                                ['value' => 'square_redesign', 'id'=>58, 'slug' => 'option_project_square_redesign'],
                                ['value' => 'playground_safety', 'id'=>59, 'slug' => 'option_project_playground_safety'],
                                ['value' => 'heat_island_mitigation', 'id'=>60, 'slug' => 'option_project_heat_island_mitigation'],
                                ['value' => 'stream_deconcretization', 'id'=>61, 'slug' => 'option_project_stream_deconcretization'],
                                ['value' => 'coastal_protection', 'id'=>62, 'slug' => 'option_project_coastal_protection'],
                                ['value' => 'stormwater_management', 'id'=>63, 'slug' => 'option_project_stormwater_management'],
                                ['value' => 'road_maintenance', 'id'=>64, 'slug' => 'option_project_road_maintenance'],
                                ['value' => 'tunnel_safety_upgrade', 'id'=>65, 'slug' => 'option_project_tunnel_safety_upgrade'],
                                ['value' => 'building_energy_retrofit', 'id'=>66, 'slug' => 'option_project_building_energy_retrofit'],
                                ['value' => 'municipal_solar_installation','id'=>67, 'slug' => 'option_project_municipal_solar_installation'],
                                ['value' => 'smart_city_sensors', 'id'=>68, 'slug' => 'option_project_smart_city_sensors'],
                                ['value' => 'digital_services_platform', 'id'=>69, 'slug' => 'option_project_digital_services_platform'],
                                ['value' => 'public_wifi_expansion', 'id'=>70, 'slug' => 'option_project_public_wifi_expansion'],
                                ['value' => 'transparency_portal', 'id'=>71, 'slug' => 'option_project_transparency_portal'],
                                ['value' => 'waste_optimization', 'id'=>72, 'slug' => 'option_project_waste_optimization'],
                                ['value' => 'recycling_expansion', 'id'=>73, 'slug' => 'option_project_recycling_expansion'],
                                ['value' => 'social_housing_renovation', 'id'=>74, 'slug' => 'option_project_social_housing_renovation'],
                                ['value' => 'affordable_housing_plan', 'id'=>75, 'slug' => 'option_project_affordable_housing_plan'],
                                ['value' => 'school_modernization', 'id'=>76, 'slug' => 'option_project_school_modernization'],
                                ['value' => 'library_digitalization', 'id'=>77, 'slug' => 'option_project_library_digitalization'],
                                ['value' => 'youth_center_expansion', 'id'=>78, 'slug' => 'option_project_youth_center_expansion'],
                                ['value' => 'elderly_care_services', 'id'=>79, 'slug' => 'option_project_elderly_care_services'],
                                ['value' => 'public_health_campaign', 'id'=>80, 'slug' => 'option_project_public_health_campaign'],
                                ['value' => 'community_center_network', 'id'=>81, 'slug' => 'option_project_community_center_network'],
                                ['value' => 'museum_expansion', 'id'=>82, 'slug' => 'option_project_museum_expansion'],
                                ['value' => 'tourism_promotion', 'id'=>83, 'slug' => 'option_project_tourism_promotion'],
                                ['value' => 'beach_sanitation', 'id'=>84, 'slug' => 'option_project_beach_sanitation'],
                            ]
                        ],
                    ]
                ],
                [
                    'id'=>85, 'slug' => 'section_priorities',
                    'questions' => [
                        [
                            'id'=>86, 'slug' => 'question_priorities_ordered',
                            'type' => 'ranking',
                            'options' => [
                                ['value' => 'car', 'id'=>87, 'slug' => 'option_priority_car'],
                                ['value' => 'pedestrian', 'id'=>88, 'slug' => 'option_priority_pedestrian'],
                                ['value' => 'cycling', 'id'=>89, 'slug' => 'option_priority_cycling'],
                                ['value' => 'green', 'id'=>90, 'slug' => 'option_priority_green'],
                                ['value' => 'family', 'id'=>91, 'slug' => 'option_priority_family'],
                                ['value' => 'buses', 'id'=>92, 'slug' => 'option_priority_buses'],
                                ['value' => 'drainage', 'id'=>93, 'slug' => 'option_priority_drainage'],
                                ['value' => 'aesthetic', 'id'=>94, 'slug' => 'option_priority_aesthetic'],
                            ]
                        ],
                        [
                            'id'=>95, 'slug' => 'question_priorities_check',
                            'type' => 'single',
                            'options' => [
                                ['value' => 'favour', 'id'=>96, 'slug' => 'option_favour'],
                                ['value' => 'neutral', 'id'=>97, 'slug' => 'option_neutral'],
                                ['value' => 'against', 'id'=>98, 'slug' => 'option_against'],
                            ]
                        ],
                    ]
                ]
            ]
        ]
    ];
    return $surveys[$id];
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
        if (str_starts_with($slug, $q)) {
            $score = max($score, 100);
            $match = 'slug_starts';
        } elseif (strpos($slug, $q) !== false) {
            $score = max($score, 70);
            $match = 'slug_contains';
        }

        // 2. match label lingua corrente
        if ($item['lang'] === $lang) {
            if (str_starts_with($label, $q)) {
                $score = max($score, 90);
                $match = 'label_starts_lang';
            } elseif (strpos($label, $q) !== false) {
                $score = max($score, 60);
                $match = 'label_contains_lang';
            }
        } else {
            // altre lingue (fallback debole)
            if (strpos($label, $q) !== false) {
                $score = max($score, 40);
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

function consulto_rest_api_init () {
    register_rest_route('consulto/v1', '/autocomplete', [
        'methods'  => 'GET',
        'callback' => 'consulto_autocomplete_handler',
        'permission_callback' => '__return_true',
        // 'permission_callback' => function () {
        //     return current_user_can('edit_posts');
        // }
    ]);
}
