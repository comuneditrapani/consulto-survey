<?php
/**
 * Test per le route REST /consulto/v1/survey/:id
 *
 * @package Consulto_Survey
 */
class SurveyRoutesTest extends WP_Test_REST_TestCase {

	private WP_REST_Server $server;
	private int $survey_id;

	public function set_up(): void {
		parent::set_up();

		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );

		$editor = self::factory()->user->create( [ 'role' => 'editor' ] );
		wp_set_current_user( $editor );

		// Crea un post consulto_survey di test
		$this->survey_id = self::factory()->post->create( [
			'post_type'   => 'consulto_survey',
			'post_status' => 'publish',
			'post_title'  => 'Survey di test',
		] );
	}

	private function request( string $method, string $route, mixed $body = null ): WP_REST_Response {
		$request = new WP_REST_Request( $method, $route );
		if ( $body !== null ) {
			$request->set_header( 'Content-Type', 'application/json' );
			$request->set_body( wp_json_encode( $body ) );
		}
		return $this->server->dispatch( $request );
	}

	// -----------------------------------------------------------------------
	// GET /consulto/v1/survey/:id
	// -----------------------------------------------------------------------

	public function test_get_survey_restituisce_struttura_vuota_se_no_schema(): void {
		$response = $this->request( 'GET', "/consulto/v1/survey/{$this->survey_id}" );

		$this->assertSame( 200, $response->get_status() );
		$data = $response->get_data();
		$this->assertSame( '', $data['title'] );
		$this->assertSame( [], $data['sections'] );
	}

	public function test_get_survey_restituisce_lo_schema_salvato(): void {
		$schema = [
			'title'    => 'Sondaggio mobilità',
			'sections' => [
				[ 'id' => 1, 'slug' => 'sec_a', 'questions' => [] ]
			],
		];
		update_post_meta( $this->survey_id, '_consulto_survey_schema', wp_json_encode( $schema, JSON_UNESCAPED_UNICODE ) );

		$response = $this->request( 'GET', "/consulto/v1/survey/{$this->survey_id}" );

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( $schema, $response->get_data() );
	}

	public function test_get_survey_richiede_autenticazione(): void {
		wp_set_current_user( 0 );

		$response = $this->request( 'GET', "/consulto/v1/survey/{$this->survey_id}" );

		$this->assertSame( 401, $response->get_status() );
	}

	public function test_get_survey_richiede_permessi_edit_posts(): void {
		$subscriber = self::factory()->user->create( [ 'role' => 'subscriber' ] );
		wp_set_current_user( $subscriber );

		$response = $this->request( 'GET', "/consulto/v1/survey/{$this->survey_id}" );

		$this->assertSame( 403, $response->get_status() );
	}

	// -----------------------------------------------------------------------
	// POST /consulto/v1/survey/:id
	// -----------------------------------------------------------------------

	public function test_post_survey_salva_lo_schema(): void {
		$schema = [
			'title'    => 'Sondaggio mobilità',
			'sections' => [
				[ 'id' => 1, 'slug' => 'sec_a', 'questions' => [] ]
			],
		];

		$response = $this->request( 'POST', "/consulto/v1/survey/{$this->survey_id}", $schema );

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( [ 'ok' => true ], $response->get_data() );

		$saved = json_decode( get_post_meta( $this->survey_id, '_consulto_survey_schema', true ), true );
		$this->assertSame( $schema, $saved );
	}

	public function test_post_survey_sovrascrive_schema_esistente(): void {
		$vecchio = [ 'title' => 'Vecchio', 'sections' => [] ];
		update_post_meta( $this->survey_id, '_consulto_survey_schema', wp_json_encode( $vecchio ) );

		$nuovo = [ 'title' => 'Nuovo', 'sections' => [ [ 'id' => 1 ] ] ];
		$this->request( 'POST', "/consulto/v1/survey/{$this->survey_id}", $nuovo );

		$saved = json_decode( get_post_meta( $this->survey_id, '_consulto_survey_schema', true ), true );
		$this->assertSame( $nuovo, $saved );
	}

	public function test_post_survey_richiede_autenticazione(): void {
		wp_set_current_user( 0 );

		$response = $this->request( 'POST', "/consulto/v1/survey/{$this->survey_id}", [ 'title' => 'X' ] );

		$this->assertSame( 401, $response->get_status() );
	}

	public function test_post_survey_richiede_permessi_edit_posts(): void {
		$subscriber = self::factory()->user->create( [ 'role' => 'subscriber' ] );
		wp_set_current_user( $subscriber );

		$response = $this->request( 'POST', "/consulto/v1/survey/{$this->survey_id}", [ 'title' => 'X' ] );

		$this->assertSame( 403, $response->get_status() );
	}

    public function test_post_survey_preserva_caratteri_utf8(): void {
        $schema = [ 'title' => 'Sondaggio mobilità', 'sections' => [] ];

        $this->request( 'POST', "/consulto/v1/survey/{$this->survey_id}", $schema );

        $saved = json_decode( get_post_meta( $this->survey_id, '_consulto_survey_schema', true ), true );
        $this->assertSame( 'Sondaggio mobilità', $saved['title'] );
    }
}
