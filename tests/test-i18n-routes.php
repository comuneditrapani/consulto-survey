<?php
/**
 * Test per le route REST /consulto/v1/i18n
 *
 * Usa WP_REST_Server per fare richieste HTTP reali contro WordPress,
 * esattamente come farebbe il client JS in src/api.js.
 *
 * @package Consulto_Survey
 */
class I18nRoutesTest extends WP_Test_REST_TestCase {

	private WP_REST_Server $server;

	public function set_up(): void {
		parent::set_up();

		// Inizializza il server REST e registra le route del plugin.
		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );

		// Utente con permessi edit_posts, necessario per tutte le route.
		$editor = self::factory()->user->create( [ 'role' => 'editor' ] );
		wp_set_current_user( $editor );

		// Partiamo da una mappa i18n pulita.
		delete_option( 'consulto_i18n_map' );
		delete_option( 'consulto_i18n_map_backup' );
	}

	public function tear_down(): void {
		delete_option( 'consulto_i18n_map' );
		delete_option( 'consulto_i18n_map_backup' );
		parent::tear_down();
	}

	// -----------------------------------------------------------------------
	// Helpers
	// -----------------------------------------------------------------------

	private function request( string $method, string $route, mixed $body = null ): WP_REST_Response {
		$request = new WP_REST_Request( $method, $route );
		if ( $body !== null ) {
			$request->set_header( 'Content-Type', 'application/json' );
			$request->set_body( wp_json_encode( $body ) );
		}
		return $this->server->dispatch( $request );
	}

	private function get_map(): array {
		return json_decode( get_option( 'consulto_i18n_map', '{}' ), true ) ?: [];
	}

	// -----------------------------------------------------------------------
	// GET /consulto/v1/i18n
	// -----------------------------------------------------------------------

	public function test_get_i18n_restituisce_mappa_vuota_se_non_ci_sono_traduzioni(): void {
		$response = $this->request( 'GET', '/consulto/v1/i18n' );

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( [], $response->get_data() );
	}

	public function test_get_i18n_restituisce_la_mappa_esistente(): void {
		$mappa = [ 'option_bus' => [ 'it' => 'Mezzi pubblici', 'en' => 'Public transport' ] ];
		update_option( 'consulto_i18n_map', wp_json_encode( $mappa ) );

		$response = $this->request( 'GET', '/consulto/v1/i18n' );

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( $mappa, $response->get_data() );
	}

	public function test_get_i18n_richiede_autenticazione_utente_non_loggato(): void {
		wp_set_current_user( 0 );

		$response = $this->request( 'GET', '/consulto/v1/i18n' );

		$this->assertSame( 401, $response->get_status() );
	}

    public function test_get_i18n_richiede_permessi_edit_posts(): void {
        $subscriber = self::factory()->user->create( [ 'role' => 'subscriber' ] );
        wp_set_current_user( $subscriber );
        
        $response = $this->request( 'GET', '/consulto/v1/i18n' );
        
        $this->assertSame( 403, $response->get_status() );
    }

	// -----------------------------------------------------------------------
	// POST /consulto/v1/i18n  — merge, non rimpiazzo
	// -----------------------------------------------------------------------

	public function test_post_i18n_aggiunge_nuovi_slug(): void {
		$response = $this->request( 'POST', '/consulto/v1/i18n', [
			'option_bus' => [ 'it' => 'Mezzi pubblici', 'en' => 'Public transport' ],
		] );

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( [ 'ok' => true ], $response->get_data() );
		$this->assertArrayHasKey( 'option_bus', $this->get_map() );
	}

	public function test_post_i18n_merge_preserva_slug_esistenti(): void {
		update_option( 'consulto_i18n_map', wp_json_encode( [
			'option_bus'  => [ 'it' => 'Mezzi pubblici' ],
			'option_bike' => [ 'it' => 'Bicicletta' ],
		] ) );

		$this->request( 'POST', '/consulto/v1/i18n', [
			'option_bus' => [ 'it' => 'Autobus', 'en' => 'Bus' ],
		] );

		$map = $this->get_map();
		$this->assertSame( [ 'it' => 'Autobus', 'en' => 'Bus' ], $map['option_bus'],
			'Lo slug inviato deve essere sovrascritto' );
		$this->assertSame( [ 'it' => 'Bicicletta' ], $map['option_bike'],
			'Gli slug non inviati devono essere preservati' );
	}

	public function test_post_i18n_salva_backup_prima_di_modificare(): void {
		$originale = [ 'option_bus' => [ 'it' => 'Mezzi pubblici' ] ];
		update_option( 'consulto_i18n_map', wp_json_encode( $originale ) );

		$this->request( 'POST', '/consulto/v1/i18n', [
			'option_bus' => [ 'it' => 'Autobus' ],
		] );

		$backup = json_decode( get_option( 'consulto_i18n_map_backup' ), true );
		$this->assertSame( $originale, $backup,
			'Il backup deve contenere la mappa prima della modifica' );
	}

	public function test_post_i18n_restituisce_400_se_body_non_e_oggetto(): void {
		$response = $this->request( 'POST', '/consulto/v1/i18n', 'stringa_non_valida' );

		$this->assertSame( 400, $response->get_status() );
	}

	// -----------------------------------------------------------------------
	// PUT /consulto/v1/i18n/:slug  — rimpiazzo atomico
	// -----------------------------------------------------------------------

	public function test_put_i18n_slug_crea_slug_nuovo(): void {
		$response = $this->request( 'PUT', '/consulto/v1/i18n/option_bus', [
			'it' => 'Mezzi pubblici', 'en' => 'Public transport',
		] );

		$this->assertSame( 200, $response->get_status() );
		$map = $this->get_map();
		$this->assertArrayHasKey( 'option_bus', $map );
	}

	public function test_put_i18n_slug_rimpiazza_completamente_le_traduzioni(): void {
		update_option( 'consulto_i18n_map', wp_json_encode( [
			'option_bus' => [ 'it' => 'Vecchio', 'en' => 'Old', 'fr' => 'Vieux' ],
		] ) );

		$this->request( 'PUT', '/consulto/v1/i18n/option_bus', [
			'it' => 'Autobus', 'en' => 'Bus',
		] );

		$map = $this->get_map();
		$this->assertSame( [ 'it' => 'Autobus', 'en' => 'Bus' ], $map['option_bus'],
			'PUT deve rimpiazzare tutte le traduzioni, non fare merge — fr deve sparire' );
	}

	public function test_put_i18n_slug_non_tocca_altri_slug(): void {
		update_option( 'consulto_i18n_map', wp_json_encode( [
			'option_bus'  => [ 'it' => 'Mezzi pubblici' ],
			'option_bike' => [ 'it' => 'Bicicletta' ],
		] ) );

		$this->request( 'PUT', '/consulto/v1/i18n/option_bus', [ 'it' => 'Autobus' ] );

		$map = $this->get_map();
		$this->assertSame( [ 'it' => 'Bicicletta' ], $map['option_bike'] );
	}

	public function test_put_i18n_slug_salva_backup_prima_di_modificare(): void {
		$originale = [ 'option_bus' => [ 'it' => 'Mezzi pubblici' ] ];
		update_option( 'consulto_i18n_map', wp_json_encode( $originale ) );

		$this->request( 'PUT', '/consulto/v1/i18n/option_bus', [ 'it' => 'Autobus' ] );

		$backup = json_decode( get_option( 'consulto_i18n_map_backup' ), true );
		$this->assertSame( $originale, $backup );
	}

	public function test_put_i18n_slug_restituisce_400_se_body_non_e_oggetto(): void {
		$response = $this->request( 'PUT', '/consulto/v1/i18n/option_bus', 'stringa_non_valida' );

		$this->assertSame( 400, $response->get_status() );
	}

	// -----------------------------------------------------------------------
	// DELETE /consulto/v1/i18n/:slug
	// -----------------------------------------------------------------------

	public function test_delete_i18n_slug_rimuove_lo_slug(): void {
		update_option( 'consulto_i18n_map', wp_json_encode( [
			'option_bus'  => [ 'it' => 'Mezzi pubblici' ],
			'option_bike' => [ 'it' => 'Bicicletta' ],
		] ) );

		$response = $this->request( 'DELETE', '/consulto/v1/i18n/option_bus' );

		$this->assertSame( 200, $response->get_status() );
		$map = $this->get_map();
		$this->assertArrayNotHasKey( 'option_bus', $map,
			'Lo slug eliminato non deve essere presente' );
		$this->assertArrayHasKey( 'option_bike', $map,
			'Gli altri slug devono essere preservati' );
	}

	public function test_delete_i18n_slug_restituisce_404_se_slug_non_esiste(): void {
		$response = $this->request( 'DELETE', '/consulto/v1/i18n/slug_inesistente' );

		$this->assertSame( 404, $response->get_status() );
	}

	public function test_delete_i18n_slug_salva_backup_prima_di_modificare(): void {
		$originale = [ 'option_bus' => [ 'it' => 'Mezzi pubblici' ] ];
		update_option( 'consulto_i18n_map', wp_json_encode( $originale ) );

		$this->request( 'DELETE', '/consulto/v1/i18n/option_bus' );

		$backup = json_decode( get_option( 'consulto_i18n_map_backup' ), true );
		$this->assertSame( $originale, $backup );
	}
}
