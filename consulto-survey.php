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

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'consulto-survey',
        plugins_url('assets/js/survey.js', __FILE__),
        [],
        null,
        true
    );

    $survey = consulto_get_survey_definiton();

    wp_add_inline_script(
        'consulto-survey-js',
        'window.consulto = window.consulto || {}; window.consulto.config = ' . json_encode($survey) . ';',
        'before'
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
