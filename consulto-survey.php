<?php

/*
 * Plugin Name: Consulto Survey
 * Description: Simple survey plugin with ranking support.
 * Version: 0.1
 * Author: Comune di Trapani
 * License: GPL2 or later
 * Physical author: Mario Frasca
 * Text Domain: consulto-survey
*/

add_shortcode('consulto_survey', 'consulto_render_survey');

require_once __DIR__.'/includes/survey.php';
require_once __DIR__.'/includes/render.php';
require_once __DIR__.'/includes/save.php';

wp_enqueue_script(
    'sortablejs',
    'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js',
    [],
    null,
    true
);

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

function consulto_register_cpt_section() {
    register_post_type('consulto_section', [
        'labels' => [
            'name' => 'Sections',
            'singular_name' => 'Section',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-screenoptions',
        'supports' => ['title', 'editor', 'page-attributes'],
        'has_archive' => false,
        'rewrite' => false,
    ]);
}

function consulto_register_cpt_question() {
    register_post_type('consulto_question', [
        'labels' => [
            'name' => 'Questions',
            'singular_name' => 'Question',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-editor-help',
        'supports' => ['title', 'editor', 'page-attributes'],
        'has_archive' => false,
        'rewrite' => false,
    ]);
}

function consulto_register_cpt_option() {
    register_post_type('consulto_option', [
        'labels' => [
            'name' => 'Options',
            'singular_name' => 'Option',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-list-view',
        'supports' => ['title', 'page-attributes'],
        'has_archive' => false,
        'rewrite' => false,
    ]);
}

add_action('init', 'consulto_register_cpt_survey');
add_action('init', 'consulto_register_cpt_section');
add_action('init', 'consulto_register_cpt_question');
add_action('init', 'consulto_register_cpt_option');

function consulto_register_acf_survey_fields() {
    acf_add_local_field_group([
        'key' => 'consulto_group_survey',
        'title' => 'Survey Fields',
        'fields' => [
            [
                'key' => 'consulto_field_survey_slug',
                'label' => 'Slug',
                'name' => 'slug',
                'type' => 'text',
            ],
            [
                'key' => 'consulto_field_survey_sections',
                'label' => 'Sezioni',
                'name' => 'sections',
                'type' => 'relationship',
                'post_type' => ['consulto_section'],
                'return_format' => 'id',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'consulto_survey',
                ],
            ],
        ],
    ]);
}

function consulto_register_acf_question_fields() {
    acf_add_local_field_group([
        'key' => 'consulto_group_question',
        'title' => 'Question Fields',
        'fields' => [
            [
                'key' => 'consulto_field_question_slug',
                'label' => 'Slug',
                'name' => 'slug',
                'type' => 'text',
            ],
            [
                'key' => 'consulto_field_question_type',
                'label' => 'Tipo',
                'name' => 'type',
                'type' => 'select',
                'choices' => [
                    'text' => 'Testo libero',
                    'single' => 'Scelta singola',
                    'multiple' => 'Scelta multipla',
                    'scale' => 'Scala (interi)',
                    'ranking' => 'Ranking',
                    'ranking-partial' => 'Ordinamento parziale',
                ],
            ],
            [
                'key' => 'consulto_field_question_min',
                'label' => 'Min',
                'name' => 'min',
                'type' => 'number',
                'conditional_logic' => [
                    [
                        [
                            'field' => 'consulto_field_question_type',
                            'operator' => '==',
                            'value' => 'scale',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'consulto_field_question_min',
                'label' => 'Min',
                'name' => 'min',
                'type' => 'number',
                'conditional_logic' => [
                    [
                        [
                            'field' => 'consulto_field_question_type',
                            'operator' => '==',
                            'value' => 'scale',
                        ],
                    ],
                ],
            ]
            [
                'key' => 'consulto_field_question_options',
                'label' => 'Opzioni',
                'name' => 'options',
                'type' => 'relationship',
                'post_type' => ['consulto_option'],
                'return_format' => 'id',
                'conditional_logic' => [
                    [
                        [
                            'field' => 'consulto_field_question_type',
                            'operator' => '==',
                            'value' => 'single',
                        ],
                        [
                            'field' => 'consulto_field_question_type',
                            'operator' => '==',
                            'value' => 'multiple',
                        ],
                        [
                            'field' => 'consulto_field_question_type',
                            'operator' => '==',
                            'value' => 'ranking',
                        ],
                        [
                            'field' => 'consulto_field_question_type',
                            'operator' => '==',
                            'value' => 'ranking-partial',
                        ],
                    ],
                ],
                'sub_fields' => [
                    [
                        'key' => 'consulto_field_option_value',
                        'label' => 'Value',
                        'name' => 'value',
                        'type' => 'text',
                    ],
                    [
                        'key' => 'consulto_field_option_slug',
                        'label' => 'Slug',
                        'name' => 'slug',
                        'type' => 'text',
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'consulto_question',
                ],
            ],
        ],
    ]);
}

add_action('acf/init', 'consulto_register_acf_survey_fields');
//add_action('acf/init', 'consulto_register_acf_section_fields'); // solved sub_fields
add_action('acf/init', 'consulto_register_acf_question_fields');
//add_action('acf/init', 'consulto_register_acf_option_fields'); // solved by sub_fields

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'consulto-survey',
        plugins_url('assets/js/survey.js', __FILE__),
        [],
        null,
        true
    );
    wp_enqueue_style(
        'consulto-survey-style',
        plugins_url('assets/css/survey.css', __FILE__)
    );
});


register_activation_hook(__FILE__, 'consulto_create_tables');

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
