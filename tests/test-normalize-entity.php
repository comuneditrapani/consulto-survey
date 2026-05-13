<?php
/**
 * Class NormalizeEntityTest extends WP_UnitTestCase
         │
         └── describe consulto_normalize_entity & consulto_t

 *
 * @package Consulto_Survey
 */

/**
 * Sample test case.
 */
class NormalizeEntityTest extends WP_UnitTestCase {
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        update_option( 'consulto_i18n_map', wp_json_encode( [
            'option_transport' => [
                'it' => 'Trasporto pubblico',
                'en' => 'Public transport',
                'es' => 'Transporte público',
                'fr' => 'Transport en commun',
                'de' => 'Öffentlicher Nahverkehr',
            ],
            'option_park' => [
                'en' => 'Public park',
            ],
            'option_culture' => [
                'it' => 'Cultura e intrattenimento',
            ],
        ] ) );
    }
    //function consulto_normalize_entity($entity, $lang, $prefix) { …

    public function test_consulto_normalize_entity_returns_null_on_missing_id() {
        $entity = [ 'slug' => 'slug' ];
        $result = consulto_normalize_entity($entity, "it", "survey");
        $this->assertNull($result);
    }
    public function test_consulto_normalize_entity_uses_slug_if_present_without_translation() {
        $entity = [ 'id' => 12, 'slug' => 'slug' ];
        $expected = [ 'id' => 12, 'slug' => 'slug', 'label' => 'slug' ];
        $result = consulto_normalize_entity($entity, "it", "survey");
        $this->assertSame( $expected, $result );
    }
    public function test_consulto_normalize_entity_prunes_extra_fields() {
        $entity = [ 'id' => 12, 'slug' => 'slug', 'extra' => 'whatever' ];
        $expected = [ 'id' => 12, 'slug' => 'slug', 'label' => 'slug' ];
        $result = consulto_normalize_entity($entity, "it", "survey");
        $this->assertSame( $expected, $result );
    }
    public function test_consulto_normalize_entity_creates_fallback_slug_if_missing() {
        $entity = [ 'id' => 42 ];
        $result = consulto_normalize_entity( $entity, 'it', 'survey' );
        $this->assertSame( 'survey_42_missing_slug', $result['slug'] );
    }

    public function test_consulto_t_restituisce_traduzione_nella_lingua_richiesta() {
        $this->assertSame( 'Trasporto pubblico', consulto_t( 'option_transport', 'it' ) );
        $this->assertSame( 'Public transport',   consulto_t( 'option_transport', 'en' ) );
        $this->assertSame( 'Transporte público', consulto_t( 'option_transport', 'es' ) );
    }
    public function test_consulto_t_fallback_a_en_se_lingua_mancante() {
        // option_park ha solo 'en'
        $this->assertSame( 'Public park', consulto_t( 'option_park', 'it' ) );
        $this->assertSame( 'Public park', consulto_t( 'option_park', 'fr' ) );
    }
    public function test_consulto_t_fallback_allo_slug_se_ne_en_ne_lingua() {
        // option_culture ha solo 'it', niente 'en'
        $this->assertSame( 'option_culture', consulto_t( 'option_culture', 'en' ) );
        $this->assertSame( 'option_culture', consulto_t( 'option_culture', 'fr' ) );
    }
    public function test_consulto_t_fallback_allo_slug_se_slug_inesistente() {
        $this->assertSame( 'slug_inesistente', consulto_t( 'slug_inesistente', 'it' ) );
    }    

    public function test_consulto_normalize_entity_slug_to_label_when_found() {
        // option_transport in it → 'Trasporto pubblico'
        $entity = [ 'id' => 12, 'slug' => 'option_transport', 'extra' => 'whatever' ];
        $expected = [ 'id' => 12, 'slug' => 'option_transport', 'label' => 'Trasporto pubblico' ];
        $result = consulto_normalize_entity($entity, "it", "survey");
        $this->assertSame( $expected, $result );
        $expected = [ 'id' => 12, 'slug' => 'option_transport', 'label' => 'Transporte público' ];
        $result = consulto_normalize_entity($entity, "es", "survey");
        $this->assertSame( $expected, $result );
    }
    public function test_consulto_normalize_entity_slug_to_english_label_when_possible() {
        // option_park in it → fallback 'Public park' (solo 'en' disponibile)
        $entity = [ 'id' => 12, 'slug' => 'option_park', 'extra' => 'whatever' ];
        $expected = [ 'id' => 12, 'slug' => 'option_park', 'label' => 'Public park' ];
        $result = consulto_normalize_entity($entity, "it", "survey");
        $this->assertSame( $expected, $result );
        $result = consulto_normalize_entity($entity, "en", "survey");
        $this->assertSame( $expected, $result );
        $result = consulto_normalize_entity($entity, "es", "survey");
        $this->assertSame( $expected, $result );
    }
    public function test_consulto_normalize_entity_slug_to_slug_when_not_even_english() {
        // option_culture in en → fallback allo slug 'option_culture' (no 'en')
        $entity = [ 'id' => 12, 'slug' => 'option_culture', 'extra' => 'whatever' ];
        $expected = [ 'id' => 12, 'slug' => 'option_culture', 'label' => 'Cultura e intrattenimento' ];
        $result = consulto_normalize_entity($entity, "it", "survey");
        $this->assertSame( $expected, $result );
        $expected = [ 'id' => 12, 'slug' => 'option_culture', 'label' => 'option_culture' ];
        $result = consulto_normalize_entity($entity, "en", "survey");
        $this->assertSame( $expected, $result );
        $result = consulto_normalize_entity($entity, "fr", "survey");
        $this->assertSame( $expected, $result );
        $result = consulto_normalize_entity($entity, "es", "survey");
        $this->assertSame( $expected, $result );
    }
    public function test_consulto_normalize_entity_slug_to_slug() {
        // option_inesistente in qualsiasi lingua → fallback allo slug
        $entity = [ 'id' => 12, 'slug' => 'option_inesistente', 'extra' => 'whatever' ];
        $expected = [ 'id' => 12, 'slug' => 'option_inesistente', 'label' => 'option_inesistente' ];
        $result = consulto_normalize_entity($entity, "it", "survey");
        $this->assertSame( $expected, $result );
        $result = consulto_normalize_entity($entity, "fr", "survey");
        $this->assertSame( $expected, $result );
        $result = consulto_normalize_entity($entity, "es", "survey");
        $this->assertSame( $expected, $result );
    }

}
