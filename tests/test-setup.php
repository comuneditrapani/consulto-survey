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

        // Verifica robusta cross-DB: scriviamo davvero nelle tabelle create.
        $insert_reply = $wpdb->insert(
            $table_replies,
            [
                'created_at' => current_time('mysql'),
                'survey_id' => 1,
            ],
            [
                '%s',
                '%d',
            ]
        );

        $this->assertNotFalse(
            $insert_reply,
            'Insert su consulto_replies fallita. last_error: ' . $wpdb->last_error
        );

        $reply_id = (int) $wpdb->insert_id;
        $this->assertGreaterThan(0, $reply_id, 'insert_id non valido per consulto_replies');

        $insert_answer = $wpdb->insert(
            $table_answers,
            [
                'reply_id' => $reply_id,
                'question_id' => 'q1',
                'value' => 'test',
            ],
            [
                '%d',
                '%s',
                '%s',
            ]
        );

        $this->assertNotFalse(
            $insert_answer,
            'Insert su consulto_answers fallita. last_error: ' . $wpdb->last_error
        );

        $answer_id = (int) $wpdb->insert_id;
        $this->assertGreaterThan(0, $answer_id, 'insert_id non valido per consulto_answers');
    }
}

class ConsultoTCacheTest extends WP_UnitTestCase {
    protected function setUp(): void {
        parent::setUp();
        update_option( 'consulto_i18n_map', wp_json_encode( [
            'option_transport' => [
                'it' => 'Trasporto pubblico',
                'en' => 'Public transport',
                'es' => 'Transporte público',
            ],
        ] ) );
        consulto_t_reset();
    }

    public function test_consulto_t_cache_persists_after_update_option() {
        $this->assertSame( 'Trasporto pubblico', consulto_t( 'option_transport', 'it' ) );
        $this->assertSame( 'Public transport',   consulto_t( 'option_transport', 'en' ) );
        $this->assertSame( 'Transporte público', consulto_t( 'option_transport', 'es' ) );
        update_option( 'consulto_i18n_map', wp_json_encode( [
            'option_transport' => [
                'it' => 'Trasporto pubblico aggiornato',
                'en' => 'Public transport, updated',
                'es' => 'Transporte público puesto al día',
            ],
        ] ) );
        $this->assertSame( 'Trasporto pubblico', consulto_t( 'option_transport', 'it' ) );
        $this->assertSame( 'Public transport',   consulto_t( 'option_transport', 'en' ) );
        $this->assertSame( 'Transporte público', consulto_t( 'option_transport', 'es' ) );
    }
    public function test_consulto_can_reset_i18n() {
        $this->assertSame( 'Trasporto pubblico', consulto_t( 'option_transport', 'it' ) );
        $this->assertSame( 'Public transport',   consulto_t( 'option_transport', 'en' ) );
        $this->assertSame( 'Transporte público', consulto_t( 'option_transport', 'es' ) );
        update_option( 'consulto_i18n_map', wp_json_encode( [
            'option_transport' => [
                'it' => 'Trasporto pubblico aggiornato',
                'en' => 'Public transport, updated',
                'es' => 'Transporte público puesto al día',
            ],
        ] ) );
        consulto_t_reset();
        $this->assertSame( 'Trasporto pubblico aggiornato', consulto_t( 'option_transport', 'it' ) );
        $this->assertSame( 'Public transport, updated',   consulto_t( 'option_transport', 'en' ) );
        $this->assertSame( 'Transporte público puesto al día', consulto_t( 'option_transport', 'es' ) );
    }

}
