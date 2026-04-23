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

add_action('init', 'consulto_register_cpt_survey');
add_action('rest_api_init', 'consulto_rest_api_init');
add_action('update_option_consulto_i18n_map', 'consulto_rebuild_i18n_flat_cache');

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

// --- interfaccia definizione survey -----------------------------

if (!defined('ABSPATH')) exit;

add_action('edit_form_after_title', function ($post) {
    if($post->post_type !== 'consulto_survey') {
        return;
    }
    echo '<div id="consulto-survey-root" data-post-id="'.esc_attr($post->ID).'"></div>';
});

add_action('admin_init', function () {
    remove_post_type_support('consulto_survey', 'editor');
});

add_action('admin_enqueue_scripts', function ($hook) {

    global $post;
    if(!$post || $post->post_type !== 'consulto_survey') {
        return;
    }

    wp_enqueue_script(
        'consulto-survey-admin',
        plugin_dir_url(__FILE__) . 'build/admin.js',
        ['wp-element'],
        filemtime(__DIR__ . '/build/admin.js'),
        true
    );

    wp_localize_script('consulto-survey-admin', 'ConsultoAPI', [
        'restUrl' => esc_url_raw(rest_url('consulto/v1')),
        'nonce'   => wp_create_nonce('wp_rest')
    ]);
});
