<?php
  require_once __DIR__.'/survey.php';
  function consulto_render_survey() {
      $survey = consulto_get_survey_definiton();
      ob_start();
?>

<script>
window.consulto = window.consulto || {}; // potrebbe venire dal JS
window.consulto.config = <?= json_encode($survey) ?>; // lo riscrivo!
</script>

<form id="consulto-form" method="post">
  <?php wp_nonce_field('consulto_survey_submit', 'consulto_nonce'); ?>
  <input type="hidden" id="consulto_payload" name="consulto_payload" value="">

  <?php foreach ($survey['sections'] as $i => $section): ?>

  <div class="consulto-section" id="section-<?= $section['slug'] ?>">
    <h3><?= $section['label'] ?></h3>
    <?php foreach ($section['questions'] as $q): ?>
    
    <div class="consulto-question">
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
      <ul class="consulto-ranking" data-question="<?= $q['id'] ?>">
        <?php foreach ($q['options'] as $opt): ?>
        <li draggable="true" data-value="<?= $opt['value'] ?>">
          <span class="consulto-handle">&#x2630;</span>
          <span class="consulto-label"><?= $opt['label'] ?></span>
        </li>
        <?php endforeach; ?>

      </ul>

      <?php
       endif;
      ?>

    </div>

    <?php endforeach; ?>
    <div class="consulto-nav">
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

  <div id="consulto-summary" style-"display:none;">
    <h3>Summary</h3>
    <div id="summary-content"></div>
    <button type="button" onclick="msBackFromSummary()" data-i18n="back_to_edit"></button>
    <button type="submit" data-i18n="submit"></button>
  </div>

</form>

<?php

    return ob_get_clean();
}
