<?php
/**
 * Class NormalizeSurveyTest extends WP_UnitTestCase
         │
         ├── describe consulto_normalize_survey — opzioni
         │   ├── normalizza value (usa slug come default)
         │   └── restituisce null se manca id
         │
         ├── describe consulto_normalize_survey — domande
         │   ├── include type (default 'text')
         │   ├── include min/max solo se presenti
         │   ├── normalizza le options
         │   └── restituisce null se manca id
         │
         ├── describe consulto_normalize_survey — sezioni
         │   ├── include questions normalizzate
         │   ├── filtra sezioni null
         │   └── restituisce null se manca id
         │
         └── describe consulto_normalize_survey — survey completa
             ├── struttura con sezioni e domande reali
             ├── filtra elementi null a tutti i livelli
             └── restituisce array vuoto se no sezioni

 *
 * @package Consulto_Survey
 */

/**
 * Sample test case.
 */
class NormalizeSurveyTest extends WP_UnitTestCase {
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        consulto_t_reset();
        update_option( 'consulto_i18n_map', wp_json_encode( [
            'option_transport' => ['it' => 'Trasporto pubblico',
                                   'en' => 'Public transport',
            ],
            'section_mobility'  => [ 'it' => 'Mobilità', 'en' => 'Mobility' ],
            'question_transport_use' => [ 'it' => 'Uso dei trasporti',
                                          'en' => 'Transport use' ],
            'option_daily'      => [ 'it' => 'Ogni giorno', 'en' => 'Daily' ],
            'option_sometimes'  => [ 'it' => 'A volte', 'en' => 'Sometimes' ],
        ] ) );
    }

    // opzioni
    public function test_normalize_option_value_falls_back_to_slug() {
        $survey = [ 'sections' => [
            [ 'id' => 1,
              'slug' => 'section_mobility',
              'questions' => [
                  [ 'id' => 10,
                    'slug' => 'question_transport_use',
                    'type' => 'radio',
                    'options' => [
                        [ 'id' => 100,
                          'slug' => 'option_daily' ] ] // niente 'value'
                  ] ] ] ] ];
        $result = consulto_normalize_survey( $survey, 'it' );
        $option = $result['sections'][0]['questions'][0]['options'][0];
        $this->assertSame( 'option_daily', $option['value'] );
    }

    // domande
    public function test_normalize_question_type_defaults_to_text() {
        $survey = [ 'sections' => [
            [ 'id' => 1,
              'slug' => 'section_mobility',
              'questions' => [
                  [ 'id' => 10,
                    'slug' => 'question_transport_use' ] ] // niente 'type'
            ] ] ];
        $result = consulto_normalize_survey( $survey, 'it' );
        $this->assertSame( 'text', $result['sections'][0]['questions'][0]['type'] );
    }

    // sezioni
    public function test_normalize_section_filters_null_questions() {
        $survey = [ 'sections' => [
            [ 'id' => 1,
              'slug' => 'section_mobility',
              'questions' => [
                  [ 'id' => 10,
                    'slug' => 'question_transport_use' ],
                  [ 'slug' => 'question_senza_id' ], // verrà filtrata
              ] ] ] ];
        $result = consulto_normalize_survey( $survey, 'it' );
        $this->assertCount( 1, $result['sections'][0]['questions'] );
    }

    // survey completa
    public function test_normalize_survey_end_to_end() {
        $survey = [ 'sections' => [
            [ 'id' => 1,
              'slug' => 'section_mobility',
              'questions' => [
                  [ 'id' => 10,
                    'slug' => 'question_transport_use',
                    'type' => 'radio',
                    'options' => [
                        [ 'id' => 100,
                          'slug' => 'option_daily',
                          'value' => 'daily' ],
                        [ 'id' => 101,
                          'slug' => 'option_sometimes',
                          'value' => 'sometimes' ],
                    ] ] ] ] ] ];
        $result = consulto_normalize_survey( $survey, 'it' );
        // testing size
        $this->assertCount( 1, $result['sections'] );
        // testing labels
        $this->assertSame( 'Mobilità', $result['sections'][0]['label'] );
        $this->assertSame( 'Uso dei trasporti', $result['sections'][0]['questions'][0]['label'] );
        $this->assertSame( 'Ogni giorno', $result['sections'][0]['questions'][0]['options'][0]['label'] );
        // testing values
        $this->assertSame( 'daily', $result['sections'][0]['questions'][0]['options'][0]['value'] );
    }
}
