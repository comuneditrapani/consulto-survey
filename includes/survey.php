<?php
function consulto_get_survey_definiton() {
    return [
        'sections' => [
            [
                'slug' => 'profile',
                'label' => 'Profile',
                'questions' => [
                    [
                        'slug' => 'profile_type',
                        'type' => 'single',
                        'label' => 'You are:',
                        'options' => [
                            ['value' => 'resident', 'label' => 'Resident'],
                            ['value' => 'resident_partial', 'label' => 'Resident part-time'],
                            ['value' => 'visitor', 'label' => 'Visitor'],
                            ['value' => 'business', 'label' => 'Business operator'],
                            ['value' => 'other', 'label' => 'Other'],
                        ]
                    ],
                    [
                        'slug' => 'profile_age',
                        'type' => 'single',
                        'label' => 'Age group:',
                        'options' => [
                            ['value' => 'under_18', 'label' => 'Under 18'],
                            ['value' => '18_29', 'label' => '18–29'],
                            ['value' => '30_44', 'label' => '30–44'],
                            ['value' => '45_65', 'label' => '45-65'],
                            ['value' => 'over_65', 'label' => 'Over 65'],
                        ]
                    ],
                ]
            ],
            [
                'slug' => 'usage',
                'label' => 'Usage',
                'questions' => [
                    [
                        'slug' => 'usage_frequency',
                        'type' => 'single',
                        'label' => 'How often do you use the historic city center?',
                        'options' => [
                            ['value' => 'daily', 'label' => 'Daily'],
                            ['value' => 'weekly', 'label' => 'Weekly'],
                        ]
                    ],
                    [
                        'slug' => 'usage_means',
                        'type' => 'single',
                        'label' => 'Main transport:',
                        'options' => [
                            ['value' => 'car', 'label' => 'Car'],
                            ['value' => 'foot', 'label' => 'Walking'],
                            ['value' => 'bus', 'label' => 'Public transport'],
                            ['value' => 'bike', 'label' => 'Bicycle'],
                        ]
                    ],
                ]
            ],
            [
                'slug' => 'perception',
                'label' => 'Perception',
                'questions' => [
                    [
                        'slug' => 'perception_spaces',
                        'type' => 'scale',
                        'label' => 'Quality of public spaces (1–5):',
                        'min' => 1,
                        'max' => 5
                    ],
                    [
                        'slug' => 'perception_mobility',
                        'type' => 'scale',
                        'label' => 'Quality of urban mobility (1–5):',
                        'min' => 1,
                        'max' => 5
                    ],
                ]
            ],
            [
                'slug' => 'priorities',
                'label' => 'Priorities',
                'questions' => [
                    [
                        'slug' => 'priorities_ordered',
                        'type' => 'ranking',
                        'label' => 'Order of priority:',
                        'options' => [
                            ['value' => 'car', 'label' => 'Improvement of car mobility'],
                            ['value' => 'pedestrian', 'label' => 'improvement of pedestrian mobility'],
                            ['value' => 'cycling', 'label' => 'Development of cycling infrastructure'],
                            ['value' => 'green', 'label' => 'Green areas'],
                            ['value' => 'family', 'label' => 'Services for families'],
                            ['value' => 'buses', 'label' => 'Public transport'],
                            ['value' => 'drainage', 'label' => 'Properness of roads'],
                            ['value' => 'aesthetic', 'label' => 'Urban aesthetic improvement'],
                        ]
                    ],
                    [
                        'slug' => 'priorities_check',
                        'type' => 'single',
                        'label' => 'Trade pedestrian spaces for more parking lots:',
                        'options' => [
                            ['value' => 'favour', 'label' => 'favour'],
                            ['value' => 'neutral', 'label' => 'neutral'],
                            ['value' => 'against', 'label' => 'against'],
                        ]
                    ],
                ]
            ]
        ]
    ];
}

