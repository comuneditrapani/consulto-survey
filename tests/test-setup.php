<?php
class SetupTest extends WP_UnitTestCase {
    public function test_consulto_survey_cpt_e_registrato() {
        $this->assertTrue( post_type_exists( 'consulto_survey' ) );
    }
    public function test_route_survey_e_registrata() {
        $routes = rest_get_server()->get_routes();
        $this->assertArrayHasKey( '/consulto/v1/survey/(?P<id>\\d+)', $routes );
    }

    public function test_route_i18n_e_registrata() {
        $routes = rest_get_server()->get_routes();
        $this->assertArrayHasKey( '/consulto/v1/i18n', $routes );
    }

    public function test_route_autocomplete_e_registrata() {
        $routes = rest_get_server()->get_routes();
        $this->assertArrayHasKey( '/consulto/v1/autocomplete', $routes );
    }

    public function test_consulto_create_tables_crea_le_tabelle() {
        global $wpdb;

        consulto_create_tables();

        $table_replies = $wpdb->prefix . 'consulto_replies';
        $table_answers = $wpdb->prefix . 'consulto_answers';

        $found_replies = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_replies))
        );
        $found_answers = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_answers))
        );

        if (!$found_replies) {
            $found_replies = $wpdb->get_var(
                $wpdb->prepare(
                    'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %s',
                    $table_replies
                )
            );
        }

        if (!$found_answers) {
            $found_answers = $wpdb->get_var(
                $wpdb->prepare(
                    'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %s',
                    $table_answers
                )
            );
        }

        $this->assertNotEmpty($found_replies, 'consulto_replies non trovata. last_error: ' . $wpdb->last_error);
        $this->assertNotEmpty($found_answers, 'consulto_answers non trovata. last_error: ' . $wpdb->last_error);
    }
}
