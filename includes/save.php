<?php

function consulto_handle_survey_submit(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    if (!isset($_POST['consulto_nonce']) ||
        !wp_verify_nonce($_POST['consulto_nonce'], 'consulto_survey_submit')) {
        return;
    }
    if (!isset($_POST['consulto_payload'])) return;

    $payload = json_decode(stripslashes($_POST['consulto_payload']), true);

    if (empty($payload) || empty($payload['survey_id']) || empty($payload['answers'])) {
        return;
    }

    $survey_id = (int) $payload['survey_id'];
    $answers   = $payload['answers'];

    // validazione dati.
    // la survey esiste come post ed è una survey.
    $survey_post = get_post($survey_id);
    if (!$survey_post || $survey_post->post_type !== 'consulto_survey') {
        return;
    }
    // sono state fornite risposte, non un formulario vuoto.
    if(!is_array($answers)) return;

    global $wpdb;

    $table_replies = $wpdb->prefix . 'consulto_replies';
    $table_answers = $wpdb->prefix . 'consulto_answers';

    $wpdb->insert($table_replies, [
        'survey_id' => $survey_id,
        'created_at' => current_time('mysql'),
    ]);

    $reply_id = $wpdb->insert_id;

    foreach ($answers as $question => $value) {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $wpdb->insert($table_answers, [
            'reply_id' => $reply_id,
            'question_id' => $question,
            'value' => $value,
        ]);
    }

    // redirect DOPO che WP è pronto
    wp_redirect($_SERVER['REQUEST_URI']);
}

/** @codeCoverageIgnore */
add_action('template_redirect', function() {
    consulto_handle_survey_submit();
    exit;
});
