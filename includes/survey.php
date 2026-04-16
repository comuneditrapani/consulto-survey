<?php
function consulto_get_survey_definiton($id) {
    /* per il momento hard coded, solo poche risposte pre-programmate;
     * a regime, i dati verranno dal DB.
     */
    switch($id) {
    case '5':
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
                ],
[
                    'slug' => 'section_hints',
                    'questions' => [
                        [
                            'slug' => 'question_hints_top',
                            'type' => 'text',
                        ],
                        [
                            'slug' => 'question_hints_tip',
                            'type' => 'text',
                        ],
                    ]
                ]
            ]
        ];
    case '42':
        return [
            'sections' => [
                [
                    'slug' => 'section_projects',
                    'questions' => [
                        [
                            'slug' => 'question_projects_selected',
                            'type' => 'ranking-partial',
                            'options' => [
                                ['value' => 'urban_mobility_plan',        'slug' => 'option_project_urban_mobility_plan'],
                                ['value' => 'pedestrian_zone_expansion',        'slug' => 'option_project_pedestrian_zone_expansion'],
                                ['value' => 'bicycle_network_upgrade',        'slug' => 'option_project_bicycle_network_upgrade'],
                                ['value' => 'public_transport_study',        'slug' => 'option_project_public_transport_study'],
                                ['value' => 'smart_traffic_system',        'slug' => 'option_project_smart_traffic_system'],
                                ['value' => 'parking_reform',        'slug' => 'option_project_parking_reform'],
                                ['value' => 'low_emission_zone',        'slug' => 'option_project_low_emission_zone'],
                                ['value' => 'center_accessibility',        'slug' => 'option_project_center_accessibility'],
                                ['value' => 'level_crossing_improvement',        'slug' => 'option_project_level_crossing_improvement'],
                                ['value' => 'waterfront_redevelopment',        'slug' => 'option_project_waterfront_redevelopment'],
                                ['value' => 'park_renovation',        'slug' => 'option_project_park_renovation'],
                                ['value' => 'urban_tree_planting',        'slug' => 'option_project_urban_tree_planting'],
                                ['value' => 'square_redesign',        'slug' => 'option_project_square_redesign'],
                                ['value' => 'playground_safety',        'slug' => 'option_project_playground_safety'],
                                ['value' => 'heat_island_mitigation',        'slug' => 'option_project_heat_island_mitigation'],
                                ['value' => 'stream_deconcretization',        'slug' => 'option_project_stream_deconcretization'],
                                ['value' => 'coastal_protection',        'slug' => 'option_project_coastal_protection'],
                                ['value' => 'stormwater_management',        'slug' => 'option_project_stormwater_management'],
                                ['value' => 'road_maintenance',        'slug' => 'option_project_road_maintenance'],
                                ['value' => 'tunnel_safety_upgrade',        'slug' => 'option_project_tunnel_safety_upgrade'],
                                ['value' => 'building_energy_retrofit',        'slug' => 'option_project_building_energy_retrofit'],
                                ['value' => 'municipal_solar_installation',        'slug' => 'option_project_municipal_solar_installation'],
                                ['value' => 'smart_city_sensors',        'slug' => 'option_project_smart_city_sensors'],
                                ['value' => 'digital_services_platform',        'slug' => 'option_project_digital_services_platform'],
                                ['value' => 'public_wifi_expansion',        'slug' => 'option_project_public_wifi_expansion'],
                                ['value' => 'transparency_portal',        'slug' => 'option_project_transparency_portal'],
                                ['value' => 'waste_optimization',        'slug' => 'option_project_waste_optimization'],
                                ['value' => 'recycling_expansion',        'slug' => 'option_project_recycling_expansion'],
                                ['value' => 'social_housing_renovation',        'slug' => 'option_project_social_housing_renovation'],
                                ['value' => 'affordable_housing_plan',        'slug' => 'option_project_affordable_housing_plan'],
                                ['value' => 'school_modernization',        'slug' => 'option_project_school_modernization'],
                                ['value' => 'library_digitalization',        'slug' => 'option_project_library_digitalization'],
                                ['value' => 'youth_center_expansion',        'slug' => 'option_project_youth_center_expansion'],
                                ['value' => 'elderly_care_services',        'slug' => 'option_project_elderly_care_services'],
                                ['value' => 'public_health_campaign',        'slug' => 'option_project_public_health_campaign'],
                                ['value' => 'community_center_network',        'slug' => 'option_project_community_center_network'],
                                ['value' => 'museum_expansion',        'slug' => 'option_project_museum_expansion'],
                                ['value' => 'tourism_promotion',        'slug' => 'option_project_tourism_promotion'],
                                ['value' => 'beach_sanitation_coastal_management',        'slug' => 'option_project_beach_sanitation_coastal_management'],
                            ]
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
        ],
        'option_30_44' => [
            'en' => '30 - 44',
        ],
        'option_45_65' => [
            'en' => '45 - 65',
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
            'it' => 'Qualità degli spazi pubblici (1–5):',
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
        'section_projects' => [
            'en' => 'Projects',
            'it' => 'Progetti',
        ],
        'question_projects_selected' => [
            'en' => 'Ordered partial choice:',
            'it' => 'Scelta parziale, ordinata',
        ],
        'option_priority_car' => [
            'en' => 'Improvement of car mobility',
        ],
        'option_priority_pedestrian' => [
            'en' => 'Improvement of pedestrian mobility',
        ],
        'option_priority_cycling' => [
            'en' => 'Development of cycling infrastructure',
            'it' => 'Sviluppo delle infrastrutture ciclistiche',
        ],
        'option_priority_green' => [
            'en' => 'Green areas',
            'it' => 'Aree verdi',
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
'option_project_social_housing_renovation' => [
  'it' => 'Ristrutturazione dell’edilizia sociale',
  'en' => 'Social housing renovation',
  'fr' => 'Rénovation du logement social',
  'de' => 'Sanierung des sozialen Wohnungsbaus',
],
'option_project_affordable_housing_plan' => [
  'it' => 'Piano per l’edilizia abitativa accessibile',
  'en' => 'Affordable housing plan',
  'fr' => 'Plan de logement abordable',
  'de' => 'Bezahlbarer Wohnungsbau',
],
'option_project_school_modernization' => [
  'it' => 'Modernizzazione delle scuole',
  'en' => 'School modernization',
  'fr' => 'Modernisation des écoles',
  'de' => 'Schulmodernisierung',
],
'option_project_library_digitalization' => [
  'it' => 'Digitalizzazione delle biblioteche',
  'en' => 'Library digitalization',
  'fr' => 'Numérisation des bibliothèques',
  'de' => 'Digitalisierung von Bibliotheken',
],
'option_project_youth_center_expansion' => [
  'it' => 'Espansione dei centri giovanili',
  'en' => 'Youth center expansion',
  'fr' => 'Extension des centres pour jeunes',
  'de' => 'Ausbau von Jugendzentren',
],
'option_project_elderly_care_services' => [
  'it' => 'Servizi per anziani',
  'en' => 'Elderly care services',
  'fr' => 'Services pour personnes âgées',
  'de' => 'Seniorenbetreuung',
],
'option_project_public_health_campaign' => [
  'it' => 'Campagne di salute pubblica',
  'en' => 'Public health campaign',
  'fr' => 'Campagne de santé publique',
  'de' => 'Gesundheitskampagne',
],
'option_project_community_center_network' => [
  'it' => 'Rete dei centri civici',
  'en' => 'Community center network',
  'fr' => 'Réseau de centres communautaires',
  'de' => 'Netzwerk von Gemeindezentren',
],
'option_project_museum_expansion' => [
  'it' => 'Espansione dei musei',
  'en' => 'Museum expansion',
  'fr' => 'Extension des musées',
  'de' => 'Museumserweiterung',
],
'option_project_tourism_promotion' => [
  'it' => 'Promozione turistica',
  'en' => 'Tourism promotion strategy',
  'fr' => 'Promotion du tourisme',
  'de' => 'Tourismusförderung',
],
'option_project_beach_sanitation_coastal_management' => [
  'it' => 'Igiene e gestione ambientale delle spiagge e delle aree costiere',
  'en' => 'Beach sanitation and coastal ecosystem management',
  'fr' => 'Assainissement des plages et gestion côtière',
  'de' => 'Strandsauberkeit und Küstenmanagement',
],
    'option_project_road_maintenance' => [
  'it' => 'Manutenzione stradale',
  'en' => 'Road maintenance',
  'fr' => 'Entretien des routes',
  'de' => 'Straßeninstandhaltung',
],
'option_project_tunnel_safety_upgrade' => [
  'it' => 'Sicurezza delle gallerie',
  'en' => 'Tunnel safety upgrade',
  'fr' => 'Sécurisation des tunnels',
  'de' => 'Tunnelsicherheit',
],
'option_project_building_energy_retrofit' => [
  'it' => 'Efficientamento energetico degli edifici pubblici',
  'en' => 'Public building energy retrofit',
  'fr' => 'Rénovation énergétique des bâtiments publics',
  'de' => 'Energetische Sanierung öffentlicher Gebäude',
],
'option_project_municipal_solar_installation' => [
  'it' => 'Installazione di pannelli solari comunali',
  'en' => 'Municipal solar installation',
  'fr' => 'Installation solaire municipale',
  'de' => 'Kommunale Solaranlagen',
],
'option_project_smart_city_sensors' => [
  'it' => 'Sensori per città intelligente',
  'en' => 'Smart city sensors',
  'fr' => 'Capteurs pour ville intelligente',
  'de' => 'Smart-City-Sensoren',
],
'option_project_digital_services_platform' => [
  'it' => 'Piattaforma dei servizi digitali',
  'en' => 'Digital services platform',
  'fr' => 'Plateforme de services numériques',
  'de' => 'Digitale Serviceplattform',
],
'option_project_public_wifi_expansion' => [
  'it' => 'Estensione Wi-Fi pubblico',
  'en' => 'Public Wi-Fi expansion',
  'fr' => 'Extension du Wi-Fi public',
  'de' => 'Ausbau des öffentlichen WLANs',
],
'option_project_transparency_portal' => [
  'it' => 'Portale della trasparenza',
  'en' => 'Transparency portal',
  'fr' => 'Portail de transparence',
  'de' => 'Transparenzportal',
],
'option_project_waste_optimization' => [
  'it' => 'Ottimizzazione raccolta rifiuti',
  'en' => 'Waste collection optimization',
  'fr' => 'Optimisation de la collecte des déchets',
  'de' => 'Optimierung der Abfallsammlung',
],
'option_project_recycling_expansion' => [
  'it' => 'Espansione della raccolta differenziata',
  'en' => 'Recycling infrastructure expansion',
  'fr' => 'Extension du recyclage',
  'de' => 'Ausbau des Recyclingsystems',
],
    'option_project_waterfront_redevelopment' => [
  'it' => 'Riqualificazione del waterfront',
  'en' => 'Waterfront redevelopment',
  'fr' => 'Réaménagement du front de mer',
  'de' => 'Neugestaltung der Uferzone',
],
'option_project_park_renovation' => [
  'it' => 'Riqualificazione dei parchi pubblici',
  'en' => 'Public park renovation',
  'fr' => 'Rénovation des parcs publics',
  'de' => 'Erneuerung öffentlicher Parks',
],
'option_project_urban_tree_planting' => [
  'it' => 'Piantumazione urbana',
  'en' => 'Urban tree planting',
  'fr' => 'Plantation d’arbres urbains',
  'de' => 'Städtische Baumpflanzungen',
],
'option_project_square_redesign' => [
  'it' => 'Riqualificazione delle piazze',
  'en' => 'Public square redesign',
  'fr' => 'Réaménagement des places',
  'de' => 'Neugestaltung von Plätzen',
],
'option_project_playground_safety' => [
  'it' => 'Sicurezza dei parchi giochi',
  'en' => 'Playground safety improvements',
  'fr' => 'Sécurité des aires de jeux',
  'de' => 'Sicherheit von Spielplätzen',
],
'option_project_heat_island_mitigation' => [
  'it' => 'Mitigazione delle isole di calore',
  'en' => 'Urban heat island mitigation',
  'fr' => 'Atténuation des îlots de chaleur',
  'de' => 'Minderung von Hitzeinseln',
],
'option_project_stream_deconcretization' => [
  'it' => 'Rinaturalizzazione dei corsi d’acqua urbani',
  'en' => 'Urban stream de-concretization',
  'fr' => 'Désartificialisation des cours d’eau urbains',
  'de' => 'Renaturierung urbaner Wasserläufe',
],
'option_project_coastal_protection' => [
  'it' => 'Protezione costiera',
  'en' => 'Coastal protection works',
  'fr' => 'Protection côtière',
  'de' => 'Küstenschutzmaßnahmen',
],
'option_project_stormwater_management' => [
  'it' => 'Gestione delle acque meteoriche',
  'en' => 'Stormwater management',
  'fr' => 'Gestion des eaux pluviales',
  'de' => 'Regenwassermanagement',
],
    'option_project_urban_mobility_plan' => [
  'it' => 'Piano della mobilità urbana',
  'en' => 'Urban mobility plan',
  'fr' => 'Plan de mobilité urbaine',
  'de' => 'Städtischer Mobilitätsplan',
],
'option_project_pedestrian_zone_expansion' => [
  'it' => 'Espansione delle zone pedonali',
  'en' => 'Pedestrian zone expansion',
  'fr' => 'Extension des zones piétonnes',
  'de' => 'Ausweitung der Fußgängerzonen',
],
'option_project_bicycle_network_upgrade' => [
  'it' => 'Potenziamento della rete ciclabile',
  'en' => 'Bicycle network upgrade',
  'fr' => 'Amélioration du réseau cyclable',
  'de' => 'Ausbau des Radwegenetzes',
],
'option_project_public_transport_study' => [
  'it' => 'Studio per il trasporto pubblico',
  'en' => 'Public transport study',
  'fr' => 'Étude des transports publics',
  'de' => 'Studie zum öffentlichen Verkehr',
],
'option_project_smart_traffic_system' => [
  'it' => 'Sistema di traffico intelligente',
  'en' => 'Smart traffic system',
  'fr' => 'Système de circulation intelligent',
  'de' => 'Intelligentes Verkehrssystem',
],
'option_project_parking_reform' => [
  'it' => 'Riforma della sosta',
  'en' => 'Parking reform',
  'fr' => 'Réforme du stationnement',
  'de' => 'Parkraumreform',
],
'option_project_low_emission_zone' => [
  'it' => 'Zona a basse emissioni',
  'en' => 'Low emission zone',
  'fr' => 'Zone à faibles émissions',
  'de' => 'Niedrigemissionszone',
],
'option_project_center_accessibility' => [
  'it' => 'Accessibilità del centro',
  'en' => 'City center accessibility',
  'fr' => 'Accessibilité du centre-ville',
  'de' => 'Zugänglichkeit der Innenstadt',
],
'option_project_level_crossing_improvement' => [
  'it' => 'Miglioramento dei passaggi a livello',
  'en' => 'Railroad level crossing improvement',
  'fr' => 'Amélioration des passages à niveau',
  'de' => 'Verbesserung von Bahnübergängen',
],
    ];
}
