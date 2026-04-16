<?php

function consulto_normalize_entity($entity, $lang, $prefix) {
    $id = $entity['id'] ?? null;
    if (!$id) return null;

    $slug = $entity['slug'] ?? $prefix . '_' . $id . '_missing_slug';

    return [
        'id' => $id,
        'slug' => $slug,
        'label' => consulto_t($slug, $lang),
    ];
}

function consulto_normalize_survey($raw, $lang, $i18n_map) {
    $fallback_lang = 'en';

    $translate = function($slug, $lang) use ($i18n_map, $fallback_lang) {
        if (!$slug) return null;

        return $i18n_map[$slug][$lang]
               ?? ($i18n_map[$slug][$fallback_lang] ?? null)
               ?? $slug;
    };

    $normalize_option = function($option, $lang) use ($translate) {
        $base = consulto_normalize_entity($option, $lang, $translate, 'option');
        if (!$base) return null;

        return array_merge($base, [
            'value' => $option['value'] ?? $base['slug'],
        ]);
    };

    $normalize_question = function($question, $lang) use ($translate) {
        $base = consulto_normalize_entity($question, $lang, $translate, 'question');
        if (!$base) return null;

        $normalized = array_merge($base, [
            'type' => $question['type'] ?? 'text',
        ]);

        if (isset($question['min'])) $normalized['min'] = $question['min'];
        if (isset($question['max'])) $normalized['max'] = $question['max'];

        // options
        if (!empty($question['options'])) {
            $normalized['options'] = array_values(array_filter(
                array_map($normalize_option, $question['options'], array_fill(0, count($question['options']), $lang))
            ));
        }

        return $normalized;
    };

    $normalize_section = function($section, $lang) use ($translate) {
        $base = consulto_normalize_entity($section, $lang, $translate, 'section');
        if (!$base) return null;

        $normalized = array_merge($base, [
            'questions' => [],
        ]);
        if (!empty($section['questions'])) {
            $normalized['questions'] = array_values(array_filter(
                array_map($normalize_question, $section['questions'], array_fill(0, count($section['questions']), $lang))
            ));
        }

        return $normalized;
    };

    $survey = [
        'sections' => []
    ];

    if (!empty($raw['sections'])) {
        $survey['sections'] = array_values(array_filter(
            array_map($normalize_section, $raw['sections'], array_fill(0, count($raw['sections']), $lang))
        ));
    }

    return $survey;
}
