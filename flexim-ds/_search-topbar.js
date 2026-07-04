/*
 * Search-with-chips в топбаре (Figma 4341:94353).
 *
 * Поведение:
 *   • Enter в инпуте → добавить чип с этим текстом.
 *   • Backspace в пустом инпуте → снять последний чип.
 *   • Клик по крестику чипа → удалить чип.
 *   • Клик по телу чипа → toggle включён/выключен (is-off → не учитывается в фильтре).
 *   • Клик по «Найти» → если в инпуте есть текст, добавить чипом, потом применить фильтр.
 *   • Клик в любое место поля → фокус на инпут.
 *   • Фокус инпута → у поля появляется --focus (2px primary-light).
 *
 * Фильтр:
 *   Скрывает строки в первой найденной .flexim-table на странице, где текст
 *   строки не содержит ВСЕ активные чипы (substring AND). Если активных нет —
 *   все строки видимы.
 *
 * Подключение:
 *   <div class="flexim-search-row flexim-search-row--fill" data-flexim-search>
 *     <div class="flexim-search flexim-search--fill">
 *       <div class="flexim-search__field">
 *         <span class="flexim-search__icon" data-flexim-icon="search" data-size="24"></span>
 *         <div class="flexim-search__chips"></div>
 *         <input class="flexim-search__input" type="text" placeholder="…">
 *       </div>
 *     </div>
 *     <button type="button" class="flexim-search__btn">Найти</button>
 *   </div>
 *
 *   Требует jQuery 3.5+ и (опционально) window.fleximIcons для перерисовки X.
 */

(function ($) {
  if (!$) return;

  function chipText($chip) {
    return $chip.contents().filter(function () {
      return this.nodeType === 3;
    }).text().trim();
  }

  // Обновляет placeholder инпута: когда в поле есть чипы — placeholder
  // прячется, остаётся только каретка. Ширина инпута не меняется.
  function updateFieldState($field) {
    var $input = $field.find('.flexim-search__input');
    if (!$input.length) return;
    if ($input.data('flexim-placeholder') == null) {
      $input.data('flexim-placeholder', $input.attr('placeholder') || '');
    }
    var hasChips = $field.find('.flexim-chip-tag').length > 0;
    $input.attr('placeholder', hasChips ? '' : $input.data('flexim-placeholder'));
  }

  function addChip($input, text) {
    var $chips = $input.closest('.flexim-search__field').find('.flexim-search__chips');
    // Дубликат → просто очищаем инпут.
    var exists = $chips.find('.flexim-chip-tag').filter(function () {
      return chipText($(this)) === text;
    }).length > 0;
    if (!exists) {
      var $chip = $(
        '<span class="flexim-chip-tag flexim-chip-tag--s"></span>'
      ).append(document.createTextNode(text + ' '))
       .append(
         '<button type="button" class="flexim-chip-dismiss" aria-label="Убрать">' +
           '<span data-flexim-icon="x-small" data-size="16" aria-hidden="true"></span>' +
         '</button>'
       );
      $chips.append($chip);
      if (window.fleximIcons) window.fleximIcons.renderAll();
    }
    $input.val('');
    updateFieldState($input.closest('.flexim-search__field'));
  }

  function applyFilter() {
    var terms = $('[data-flexim-search] .flexim-chip-tag').not('.is-off').map(function () {
      return chipText($(this)).toLowerCase();
    }).get().filter(Boolean);

    var $rows = $('.app-content .flexim-table tbody tr');
    if (!$rows.length) return;

    if (!terms.length) {
      $rows.show();
      return;
    }
    $rows.each(function () {
      var rowText = $(this).text().toLowerCase();
      var match = terms.every(function (term) { return rowText.indexOf(term) !== -1; });
      $(this).toggle(match);
    });
  }

  $(document)
    .on('click', '[data-flexim-search] .flexim-chip-dismiss', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var $field = $(this).closest('.flexim-search__field');
      $(this).closest('.flexim-chip-tag').remove();
      updateFieldState($field);
      applyFilter();
    })
    .on('click', '[data-flexim-search] .flexim-chip-tag', function (e) {
      if ($(e.target).closest('.flexim-chip-dismiss').length) return;
      $(this).toggleClass('is-off');
      applyFilter();
    })
    .on('click', '[data-flexim-search] .flexim-search__field', function (e) {
      if ($(e.target).is('input, button') || $(e.target).closest('.flexim-chip-tag').length) return;
      $(this).find('.flexim-search__input').trigger('focus');
    })
    .on('focusin', '[data-flexim-search] .flexim-search__input', function () {
      $(this).closest('.flexim-search__field').addClass('flexim-search__field--focus');
    })
    .on('focusout', '[data-flexim-search] .flexim-search__input', function () {
      $(this).closest('.flexim-search__field').removeClass('flexim-search__field--focus');
    })
    .on('keydown', '[data-flexim-search] .flexim-search__input', function (e) {
      var $input = $(this);
      var val = ($input.val() || '').trim();
      if (e.key === 'Enter') {
        e.preventDefault();
        if (val) addChip($input, val);
        applyFilter();
      } else if (e.key === 'Backspace' && !val) {
        var $field = $input.closest('.flexim-search__field');
        var $chips = $field.find('.flexim-chip-tag');
        if ($chips.length) {
          $chips.last().remove();
          updateFieldState($field);
          applyFilter();
        }
      }
    })
    // Клик по «×» справа в поле — снять все чипы и очистить инпут.
    .on('click', '[data-flexim-search] .flexim-search__clear-all', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var $field = $(this).closest('.flexim-search__field');
      $field.find('.flexim-chip-tag').remove();
      $field.find('.flexim-search__input').val('').trigger('focus');
      updateFieldState($field);
      applyFilter();
    });

  // Прогон на старте, чтобы преcell-чипы (например, «123» в разметке) сразу
  // применились к таблице и спрятали placeholder.
  $(function () {
    $('[data-flexim-search] .flexim-search__field').each(function () {
      updateFieldState($(this));
    });
    applyFilter();
  });
})(window.jQuery);
