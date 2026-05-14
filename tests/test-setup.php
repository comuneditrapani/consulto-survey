<?php
class SetupTest extends WP_UnitTestCase {
    public function test_consulto_survey_cpt_e_registrato() {
        $this->assertTrue( post_type_exists( 'consulto_survey' ) );
    }
    public function test_route_survey_e_registrata() {
        $routes = rest_get_server()->get_routes();
        $this->assertArrayHasKey( '/consulto/v1/survey/(?P<id>\d+)', $routes );
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
        $this->assertSame(
            $wpdb->prefix . 'consulto_replies',
            $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}consulto_replies'" )
        );
        $this->assertSame(
            $wpdb->prefix . 'consulto_answers',
            $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}consulto_answers'" )
        );
    }

}
