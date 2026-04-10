<?php
function consulto_get_survey_definiton() {
    /* per il momento hard coded; i dati verranno dal DB.
     */
    return [
        'sections' => [
            [
                'slug' => 'section_profile',
                'questions' => [
                    [
                        'slug' => 'question_profile_type',
                        'type' => 'single',
                        'options' => [
                            ['value' => 'resident',          'slug' => 'option_resident'],
                            ['value' => 'resident_partial',  'slug' => 'option_resident_partial'],
                            ['value' => 'visitor',           'slug' => 'option_visitor'],
                            ['value' => 'business',          'slug' => 'option_business'],
                            ['value' => 'other',             'slug' => 'option_other'],
                        ]
                    ],
                    [
                        'slug' => 'question_profile_age',
                        'type' => 'single',
                        'options' => [
                            ['value' => 'under_18', 'slug' => 'option_under_18'],
                            ['value' => '18_29',    'slug' => 'option_18_29'],
                            ['value' => '30_44',    'slug' => 'option_30_44'],
                            ['value' => '45_65',    'slug' => 'option_45_65'],
                            ['value' => 'over_65',  'slug' => 'option_over_65'],
                        ]
                    ],
                ]
            ],
            [
                'slug' => 'section_usage',
                'questions' => [
                    [
                        'slug' => 'question_usage_frequency',
                        'type' => 'single',
                        'options' => [
                            ['value' => 'daily',  'slug' => 'option_daily'],
                            ['value' => 'weekly', 'slug' => 'option_weekly'],
                        ]
                    ],
                    [
                        'slug' => 'question_usage_means',
                        'type' => 'single',
                        'options' => [
                            ['value' => 'car',  'slug' => 'option_car'],
                            ['value' => 'foot', 'slug' => 'option_foot'],
                            ['value' => 'bus',  'slug' => 'option_bus'],
                            ['value' => 'bike', 'slug' => 'option_bike'],
                        ]
                    ],
                ]
            ],
            [
                'slug' => 'section_perception',
                'questions' => [
                    [
                        'slug' => 'question_perception_spaces',
                        'type' => 'scale',
                        'min' => 1,
                        'max' => 5
                    ],
                    [
                        'slug' => 'question_perception_mobility',
                        'type' => 'scale',
                        'min' => 1,
                        'max' => 5
                    ],
                ]
            ],
            [
                'slug' => 'section_priorities',
                'questions' => [
                    [
                        'slug' => 'question_priorities_ordered',
                        'type' => 'ranking',
                        'options' => [
                            ['value' => 'car',        'slug' => 'option_priority_car'],
                            ['value' => 'pedestrian', 'slug' => 'option_priority_pedestrian'],
                            ['value' => 'cycling',    'slug' => 'option_priority_cycling'],
                            ['value' => 'green',      'slug' => 'option_priority_green'],
                            ['value' => 'family',     'slug' => 'option_priority_family'],
                            ['value' => 'buses',      'slug' => 'option_priority_buses'],
                            ['value' => 'drainage',   'slug' => 'option_priority_drainage'],
                            ['value' => 'aesthetic',  'slug' => 'option_priority_aesthetic'],
                        ]
                    ],
                    [
                        'slug' => 'question_priorities_check',
                        'type' => 'single',
                        'options' => [
                            ['value' => 'favour',  'slug' => 'option_favour'],
                            ['value' => 'neutral', 'slug' => 'option_neutral'],
                            ['value' => 'against', 'slug' => 'option_against'],
                        ]
                    ],
                ]
            ]
        ]
    ];
}

function consulto_get_i18n_map() {
    /* per il momento hard coded; i dati verranno dal DB.
     */
    return [
        'section_profile' => [
            'en' => 'Profile',
            'it' => 'Profilo',
        ],
        'question_profile_type' => [
            'en' => 'You are:',
            'it' => 'Sei:',
        ],
        'option_resident' => [
            'en' => 'Resident',
            'it' => 'Residente',
        ],
        'option_resident_partial' => [
            'en' => 'Part-time resident',
            'it' => 'Residente stagionale',
        ],
        'option_visitor' => [
            'en' => 'Visitor',
            'it' => 'Turista',
        ],
        'option_business' => [
            'en' => 'Business operator',
            'it' => 'Operatore turistico',
        ],
        'option_other' => [
            'en' => 'Other',
            'it' => 'Altro',
        ],
        'question_profile_age' => [
            'en' => 'Your age:',
            'it' => 'La tua età',
        ],
        'option_under_18' => [
            'en' => 'under 18',
            'it' => 'meno di 18',
        ],
        'option_18_29' => [
            'en' => '18 - 29',
            'it' => '18 - 29',
            'fr' => '18 - 29',
        ],
        'option_30_44' => [
            'en' => '30 - 44',
            'it' => '30 - 44',
            'fr' => '30 - 44',
        ],
        'option_45_65' => [
            'en' => '45 - 65',
            'it' => '45 - 65',
            'fr' => '45 - 65',
        ],
        'option_over_65' => [
            'en' => 'over 65',
            'it' => 'più di 65',
        ],
        'section_usage' => [
            'en' => 'Usage',
        ],
        'question_usage_frequency' => [
            'en' => 'How often do you use the historic city center?',
            'it' => 'Con che frequenza usi il centro storico?',
        ],
        'option_daily' => [
            'en' => 'Daily',
            'it' => 'Ogni giorno',
        ],
        'option_weekly' => [
            'en' => 'Weekly',
            'it' => 'Ogni settimana',
        ],
        'question_usage_means' => [
            'en' => 'Main transport:',
            'it' => 'Principale mezzo di trasporto',
        ],
        'option_car' => [
            'en' => 'Car',
            'it' => 'Auto privata',
        ],
        'option_foot' => [
            'en' => 'Walking',
            'it' => 'A piedi',
        ],
        'option_bus' => [
            'en' => 'Public transport',
            'it' => 'Mezzi pubblici',
        ],
        'option_bike' => [
            'en' => 'Bicycle',
            'it' => 'Bicicletta',
        ],
        'section_perception' => [
            'en' => 'Perception',
            'it' => 'Percezione',
        ],
        'question_perception_spaces' => [
            'en' => 'Quality of public spaces (1–5):',
        ],
        'question_perception_mobility' => [
            'en' => 'Quality of urban mobility (1–5):',
        ],
        'section_priorities' => [
            'en' => 'Priorities',
            'it' => 'Priorità',
        ],
        'question_priorities_ordered' => [
            'en' => 'Order of priorities:',
            'it' => 'Ordine di priorità',
        ],
        'option_priority_car' => [
            'en' => 'Improvement of car mobility',
        ],
        'option_priority_pedestrian' => [
            'en' => 'Improvement of pedestrian mobility',
        ],
        'option_priority_cycling' => [
            'en' => 'Development of cycling infrastructure',
        ],
        'option_priority_green' => [
            'en' => 'Green areas',
        ],
        'option_priority_family' => [
            'en' => 'Services for families',
        ],
        'option_priority_buses' => [
            'en' => 'Public transport',
            'it' => 'Trasporto pubblico',
        ],
        'option_priority_drainage' => [
            'en' => 'Sewer system and drainage',
            'it' => 'Rete fognaria e drenaggio',
        ],
        'option_priority_cleanliness' => [
            'en' => 'Street cleanliness and hygiene',
            'it' => 'Pulizia e igiene delle strade',
        ],
        'option_priority_aesthetic' => [
            'en' => 'Urban aesthetic improvement',
        ],
        'question_priorities_check' => [
            'en' => 'Trade pedestrian spaces for more parking lots:',
        ],
        'option_favour' => [
            'en' => 'Favour',
            'it' => 'a favore',
        ],
        'option_neutral' => [
            'en' => 'Neutral',
            'it' => 'indifferente',
        ],
        'option_against' => [
            'en' => 'Against',
            'it' => 'contrario',
        ],
    ];
}
