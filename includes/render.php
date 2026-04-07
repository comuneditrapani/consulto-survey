<?php

function ms_render_survey() {

    $survey = [
        'sections' => [
            [
                'id' => 'A',
                'label' => 'Profile',
                'questions' => [
                    [
                        'id' => 'Q1',
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
                        'id' => 'Q2',
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
                'id' => 'B',
                'label' => 'Usage',
                'questions' => [
                    [
                        'id' => 'Q3',
                        'type' => 'single',
                        'label' => 'How often do you use the historic city center?',
                        'options' => [
                            ['value' => 'daily', 'label' => 'Daily'],
                            ['value' => 'weekly', 'label' => 'Weekly'],
                        ]
                    ],
                    [
                        'id' => 'Q4',
                        'type' => 'single',
                        'label' => 'Main transport:',
                        'options' => [
                            ['value' => 'car', 'label' => 'Car'],
                            ['value' => 'foot', 'label' => 'Walking'],
                            ['value' => 'bus', 'label' => 'Public transport'],
                            ['value' => 'bike', 'label' => 'bicycle'],
                        ]
                    ],
                ]
            ],
            [
                'id' => 'C',
                'label' => 'Perception',
                'questions' => [
                    [
                        'id' => 'Q5',
                        'type' => 'scale',
                        'label' => 'Quality of public spaces (1–5):',
                        'min' => 1,
                        'max' => 5
                    ],
                    [
                        'id' => 'Q6',
                        'type' => 'scale',
                        'label' => 'Quality of urban mobility (1–5):',
                        'min' => 1,
                        'max' => 5
                    ],
                ]
            ],
            [
                'id' => 'D',
                'label' => 'Priorities',
                'questions' => [
                    [
                        'id' => 'Q7',
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
                        'id' => 'Q8',
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

    ob_start();
?>

<form method="post">
  <?php wp_nonce_field('ms_survey_submit', 'ms_nonce'); ?>
  <input type="hidden" name="ms_form_submitted" value="1">

  <?php foreach ($survey['sections'] as $i => $section): ?>

  <div class="ms-section" id="section-<?= $section['id'] ?>">
    <h3><?= $section['label'] ?></h3>
    <?php foreach ($section['questions'] as $q): ?>
    
    <div class="ms-question">
      <p><?= $q['label'] ?></p>
      <?php
        // SINGLE CHOICE
        if ($q['type'] === 'single'):
            foreach ($q['options'] as $opt):
            ?>
      <label>
        <input type="radio"
               data-question="<?= $q['id'] ?>"
               name="tmp_<?= $q['id'] ?>"
               value="<?= $opt['value'] ?>">
        <?= $opt['label'] ?>
      </label><br>
      <?php
           endforeach;
           
        // SCALE
        elseif ($q['type'] === 'scale'):
            for ($j = $q['min']; $j <= $q['max']; $j++):
      ?>
      <label>
        <input type="radio"
               data-question="<?= $q['id'] ?>"
               name="tmp_<?= $q['id'] ?>"
               value="<?= $j ?>">
        <?= $j ?>
      </label>

      <?php
            endfor;
        // RANKING
        elseif ($q['type'] === 'ranking'):
        ?>
      <ul class="ms-ranking" data-question="<?= $q['id'] ?>">
        <?php foreach ($q['options'] as $opt): ?>
        <li draggable="true" data-value="<?= $opt['value'] ?>">
          <span class="ms-handle">&#x2630;</span>
          <span class="ms-label"><?= $opt['label'] ?></span>
        </li>
        <?php endforeach; ?>

      </ul>

      <?php
       endif;
      ?>

    </div>

    <?php endforeach; ?>
    <div class="ms-nav">
      <?php if ($i > 0): ?>
      <button type="button" data-i18n="prev" onclick="msPrev()"></button>
      <?php endif; ?>
      <?php if ($i < count($survey['sections']) - 1): ?>
      <button type="button" data-i18n="next" onclick="msNext()"></button>
      <?php else: ?>
      <button type="button" data-i18n="review" onclick="msGoToSummary()"></button>
      <?php endif; ?>
    </div>

  </div>

  <?php endforeach; ?>

  <div id="ms-summary" style-"display:none;">
    <h3>Summary</h3>
    <div id="summary-content"></div>
    <button type="button" onclick="msBackFromSummary()" data-i18n="back_to_edit"></button>
    <button type="submit" data-i18n="submit"></button>
  </div>

</form>

<?php

    return ob_get_clean();
}
