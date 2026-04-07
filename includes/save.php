<?php

add_action('template_redirect', function() {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    if (!isset($_POST['consulto_form_submitted'])) return;

    if (!isset($_POST['consulto_nonce']) ||
        !wp_verify_nonce($_POST['consulto_nonce'], 'consulto_survey_submit')) {
        return;
    }

    global $wpdb;

    $table_replies = $wpdb->prefix . 'consulto_replies';
    $table_answers = $wpdb->prefix . 'consulto_answers';

    $wpdb->insert($table_replies, [
        'created_at' => current_time('mysql'),
    ]);

    $reply_id = $wpdb->insert_id;

    // esempio minimale
    if (isset($_POST['q1_profile'])) {
        $wpdb->insert($table_answers, [
            'reply_id' => $reply_id,
            'question_id' => 'Q1',
            'value' => $_POST['q1_profile'],
        ]);
    }

    // redirect DOPO che WP è pronto
    wp_redirect($_SERVER['REQUEST_URI']);
    exit;
});


