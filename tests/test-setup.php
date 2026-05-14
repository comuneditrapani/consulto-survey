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
