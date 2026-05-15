<?php
/**
 * Test per includes/save.php
 *
 * @package Consulto_Survey
 */
class SaveTest extends WP_UnitTestCase {

    private int $survey_id;

    public function set_up(): void {
        parent::set_up();

        consulto_create_tables();

        // Crea una survey di test
        $this->survey_id = self::factory()->post->create( [
            'post_type'   => 'consulto_survey',
            'post_status' => 'publish',
        ] );

        // Schema: una sezione, una domanda a scelta, una domanda testo
        update_post_meta( $this->survey_id, '_consulto_survey_schema', wp_json_encode( [
            'sections' => [ [
                'id'   => 1,
                'slug' => 'section_a',
                'questions' => [
                    [
                        'id'   => 'q1',
                        'slug' => 'question_choice',
                        'type' => 'radio',
                        'options' => [
                            [ 'id' => 'o1', 'slug' => 'option_a', 'value' => 'a' ],
                            [ 'id' => 'o2', 'slug' => 'option_b', 'value' => 'b' ],
                        ],
                    ],
                    [
                        'id'   => 'q2',
                        'slug' => 'question_text',
                        'type' => 'text',
                    ],
                ],
            ] ],
        ] ) );
    }

    public function tear_down(): void {
        unset( $_SERVER['REQUEST_METHOD'] );
        unset( $_POST['consulto_nonce'] );
        unset( $_POST['consulto_payload'] );
        parent::tear_down();
    }

    public function test_submit_salva_reply_e_answers(): void {
        global $wpdb;

        $_SERVER['REQUEST_METHOD']  = 'POST';
        $_POST['consulto_nonce']    = wp_create_nonce( 'consulto_survey_submit' );
        $_POST['consulto_payload']  = json_encode( [
            'survey_id' => $this->survey_id,
            'answers'   => [
                'q1' => 'a',
                'q2' => 'testo libero',
            ],
        ] );

        consulto_handle_survey_submit();

        // verifica reply
        $table_replies = $wpdb->prefix . 'consulto_replies';
        $reply = $wpdb->get_row( "SELECT * FROM $table_replies ORDER BY id DESC LIMIT 1" );
        $this->assertNotNull( $reply );
        $this->assertSame( (string) $this->survey_id, $reply->survey_id );

        // verifica answers
        $table_answers = $wpdb->prefix . 'consulto_answers';
        $answers = $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM $table_answers WHERE reply_id = %d", $reply->id )
        );
        $this->assertCount( 2, $answers );

        $by_question = array_column( $answers, 'value', 'question_id' );
        $this->assertSame( 'a', $by_question['q1'] );
        $this->assertSame( 'testo libero', $by_question['q2'] );
    }
}
