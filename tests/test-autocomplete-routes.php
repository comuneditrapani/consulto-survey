<?php
/**
 * Test per la route REST GET /consulto/v1/autocomplete
 *
 * @package Consulto_Survey
 */
class AutocompleteRouteTest extends WP_Test_REST_TestCase {

    private WP_REST_Server $server;

    public function setUp(): void {
        parent::setUp();

        global $wp_rest_server;
        $wp_rest_server = new WP_REST_Server();
        $this->server   = $wp_rest_server;
        do_action( 'rest_api_init' );

        // autocomplete è pubblico, nessun utente necessario
        wp_set_current_user( 0 );

        // reset cache e mappa
        consulto_t_reset();
        delete_transient( 'consulto_i18n_flat' );
        update_option( 'consulto_i18n_map', wp_json_encode( [
            'option_transport' => [
                'it' => 'Trasporto pubblico',
                'en' => 'Public transport',
                'es' => 'Transporte público',
            ],
            'option_park' => [
                'it' => 'Parco pubblico',
                'en' => 'Public park',
                'fr' => 'Option parc public',
            ],
            'section_mobility' => [
                'it' => 'Mobilità urbana',
                'en' => 'Urban mobility',
            ],
        ] ) );
    }

    // simplify access to creating and dispatching WP_REST_Request
    private function autocomplete( string $q, string $lang = 'it' ): array {
        $request = new WP_REST_Request( 'GET', '/consulto/v1/autocomplete' );
        $request->set_param( 'q', $q );
        $request->set_param( 'lang', $lang );
        $response = $this->server->dispatch( $request );
        return $response->get_data();
    }

    // -----------------------------------------------------------------------
    // query vuota
    // -----------------------------------------------------------------------

    public function test_query_vuota_restituisce_array_vuoto(): void {
        $result = $this->autocomplete( '' );
        $this->assertSame( [], $result );
    }

    // -----------------------------------------------------------------------
    // matching e scoring
    // -----------------------------------------------------------------------

    public function test_slug_equals_ha_score_100(): void {
        $result = $this->autocomplete( 'option_transport' );
        $this->assertSame( 'option_transport', $result[0]['slug'] );
        $this->assertSame( 100, $result[0]['score'] );
        $this->assertSame( 'slug_equals', $result[0]['match'] );
    }

    public function test_slug_ends_ha_score_90(): void {
        $result = $this->autocomplete( 'transport' );
        $hit = array_filter( $result, fn($r) => $r['slug'] === 'option_transport' );
        $hit = array_values( $hit )[0];
        $this->assertSame( 90, $hit['score'] );
        $this->assertSame( 'slug_ends', $hit['match'] );
    }

    public function test_slug_starts_ha_score_70(): void {
        $result = $this->autocomplete( 'option' );
        $this->assertCount(2, $result);
        $hit = array_filter( $result, fn($r) => $r['slug'] === 'option_transport' );
        $tophit = array_values( $hit )[0];
        $this->assertSame( 70, $tophit['score'] );
        $this->assertSame( 'slug_starts', $tophit['match'] );
    }

    // -----------------------------------------------------------------------
    // ordinamento
    // -----------------------------------------------------------------------

    public function test_risultati_ordinati_per_score_decrescente(): void {
        $result = $this->autocomplete( 'tion' );
        $this->assertCount(3, $result);
        $scores = array_column( $result, 'score' );
        $sorted = $scores;
        rsort( $sorted );
        $this->assertSame( $sorted, $scores );
    }

    // -----------------------------------------------------------------------
    // deduplicazione
    // -----------------------------------------------------------------------

    public function test_slug_appare_una_sola_volta(): void {
        $result = $this->autocomplete( 'option' );
        $this->assertCount(2, $result);
        $slugs = array_column( $result, 'slug' );
        $this->assertSame( count( $slugs ), count( array_unique( $slugs ) ) );
    }

    public function test_on_starting_label_chosen_language(): void {
        $results = $this->autocomplete( 'transpo', 'es' );
        $hits = array_values(
            array_filter($results, fn($r) => $r['slug'] === 'option_transport'));
        $this->assertSame([
            ['slug' => 'option_transport',
             'label' => 'Transporte público',
             'lang' => 'es',
             'match' => 'label_starts_lang',
             'score' => 80,
            ]], $hits);
    }
    
    public function test_current_lang_label_match_wins_over_other_lang_in_match_field(): void {
        $results = $this->autocomplete( 'transport', 'it' );
        $hits = array_values(
            array_filter( $results, fn($r) => $r['slug'] === 'option_transport' ) );
        
        $this->assertSame(['slug' => 'option_transport',
                           'label' => 'Trasporto pubblico',
                           'lang' => 'it',
                           'match' => 'slug_ends',
                           'score' => 90], $hits[0]);
    }

    public function test_current_lang_label_contains_not_starts(): void {
        $results = $this->autocomplete( 'pubblico', 'it' );
        $hits = array_values(
            array_filter( $results, fn($r) => $r['slug'] === 'option_transport' ) );
        
        $this->assertSame(['slug' => 'option_transport',
                           'label' => 'Trasporto pubblico',
                           'lang' => 'it',
                           'match' => 'label_contains_lang',
                           'score' => 60], $hits[0]);
    }

}
