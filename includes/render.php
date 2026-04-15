<?php
  require_once __DIR__.'/survey.php';

  function consulto_t($slug, $lang = 'it') {
      static $map = null;
      // l'inizializzazione statica con chiamata a funzione è
      // supportata solo da PHP 8.1 in poi.
      if ($map === null) {
          $map = consulto_get_i18n_map();
      }

      return $map[$slug][$lang]
      ?? $map[$slug]['en']
      ?? $slug;
  }

  function consulto_render_survey($atts = []) {
      $atts = shortcode_atts([
          'survey' => ''
      ], $atts);

      if (empty($atts['survey'])) {
          return '<p>Survey non specificata</p>';
      }

      $survey = consulto_get_survey_definiton($atts['survey']);

      if (!$survey) {
          return '<p>Survey non trovata</p>';
      }
      ob_start();
?>

<script>
window.consulto = window.consulto || {}; // potrebbe venire dal JS
window.consulto.config = <?= json_encode($survey) ?>; // lo riscrivo!
</script>

<form id="consulto-form" method="post" data-config="<?= esc_attr(json_encode($survey)) ?>">
  <?php wp_nonce_field('consulto_survey_submit', 'consulto_nonce'); ?>
  <input type="hidden" id="consulto-payload" name="consulto_payload" value="">

  <?php foreach ($survey['sections'] as $i => $section): ?>

  <div class="consulto-section" id="section-<?= esc_attr($section['slug']) ?>">
    <h3><?= esc_html(consulto_t($section['slug'])) ?></h3>
    <?php foreach ($section['questions'] as $q): ?>

    <div class="consulto-question">
      <p><?= esc_html(consulto_t($q['slug'])) ?></p>
      <?php
        // SINGLE CHOICE
        if ($q['type'] === 'single'):
            foreach ($q['options'] as $opt):
            ?>
      <label>
        <input type="radio"
               data-question="<?= esc_attr($q['slug']) ?>"
               name="<?= esc_attr($q['slug']) ?>"
               value="<?= esc_attr($opt['value']) ?>">
        <?= esc_html(consulto_t($opt['slug'])) ?>
      </label><br>
      <?php
           endforeach;

        // SCALE
        elseif ($q['type'] === 'scale'):
            for ($j = $q['min']; $j <= $q['max']; $j++):
      ?>
      <label>
        <input type="radio"
               data-question="<?= esc_attr($q['slug']) ?>"
               name="<?= esc_attr($q['slug']) ?>"
               value="<?= $j ?>">
        <?= $j ?>
      </label>

      <?php
            endfor;
        // RANKING
        elseif ($q['type'] === 'ranking'):
        ?>
      <ul class="consulto-ranking" data-question="<?= esc_attr($q['slug']) ?>">
        <?php foreach ($q['options'] as $opt): ?>
        <li data-value="<?= esc_attr($opt['value']) ?>">
          <?= esc_html(consulto_t($opt['slug'])) ?>
        </li>
        <?php endforeach; ?>
      </ul>
      <label>
        <input type="checkbox" id="<?= esc_attr($q['slug']) ?>-enabled">
        <span data-i18n="ranking_is_valid"></span>
      </label>
      <?php
        elseif ($q['type'] === 'ranking-partial'):
        ?>
      <div class="consulto-ranking-widget">
        <div class="consulto-ranking-selected">
          <h4 data-i18n="ranking_selection"></h4>
          <div class="consulto-selected-spacer"></div>
          <ul class="consulto-list consulto-selected" data-question="<?= esc_attr($q['slug']) ?>"></ul>
        </div>
        <div class="consulto-ranking-pool">
          <h4 data-i18n="ranking_pool"></h4>
          <input type="text" class="consulto-pool-filter">
          <ul class="consulto-list consulto-pool">
            <?php foreach ($q['options'] as $opt): ?>
            <li data-value="<?= esc_attr($opt['value']) ?>">
              <?= esc_html(consulto_t($opt['slug'])) ?>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <?php
        elseif ($q['type'] === 'textarea'):
      ?>
      <textarea
        data-question="<?= esc_attr($q['slug']) ?>"
        placeholder="<?= esc_html(consulto_t($q['slug'])) ?>"
        ></textarea>
      <?php
       endif;
      ?>

    </div>

    <?php endforeach; ?>
    <div class="consulto-nav">
      <?php if ($i > 0): ?>
      <button type="button" data-i18n="prev" onclick="c.ui.prev()"></button>
      <?php endif; ?>
      <?php if ($i < count($survey['sections']) - 1): ?>
      <button type="button" data-i18n="next" onclick="c.ui.next()"></button>
      <?php else: ?>
      <button type="button" data-i18n="review" onclick="c.ui.gotoSummary()"></button>
      <?php endif; ?>
    </div>

  </div>

  <?php endforeach; ?>

  <div id="consulto-summary" style="display:none;">
    <h3>Summary</h3>
    <div id="summary-content"></div>
    <button type="button" onclick="c.ui.backFromSummary()" data-i18n="back_to_edit"></button>
    <button type="submit" data-i18n="submit"></button>
  </div>

</form>

<?php

    return ob_get_clean();
}
