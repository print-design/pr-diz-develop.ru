/*
 * Самопроверка DS-хэдера (только develop). Неинвазивный хэдер: рейка fixed слева,
 * верхняя панель sticky сверху, контент НЕ тронут. Показывает бейдж справа-внизу
 * + отчёт в консоли. Ничего не меняет на странице.
 */
(function () {
  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }
  ready(function () {
    var rail = document.querySelector('.flexim-nav-rail');
    if (!rail) return; // не DS-страница

    var topbar = document.querySelector('.app-topbar');
    var cs = function (el) { return el ? getComputedStyle(el) : null; };
    var zi = function (el) { var v = el ? parseInt(cs(el).zIndex, 10) : NaN; return isNaN(v) ? 0 : v; };
    var checks = [];
    var add = function (name, ok, got) { checks.push({ name: name, ok: !!ok, got: got == null ? '' : String(got) }); };

    add('Рейка есть', !!rail);
    add('Ширина рейки = 60px', Math.round(parseFloat(cs(rail).width)) === 60, cs(rail).width);
    add('Рейка зафиксирована (fixed)', cs(rail).position === 'fixed', cs(rail).position);
    add('Рейка — верхний слой', topbar && zi(rail) > zi(topbar), (rail && topbar) ? ('рейка ' + zi(rail) + ' / топбар ' + zi(topbar)) : '');
    add('У рейки есть тень', cs(rail).boxShadow && cs(rail).boxShadow !== 'none', cs(rail).boxShadow);
    add('Верхняя панель прилипает (sticky)', topbar && /sticky|fixed/.test(cs(topbar).position), topbar && cs(topbar).position);
    add('Иконки рейки отрисованы (SVG)', !!rail.querySelector('svg'));

    // --- Состав хэдера: сверка табов раздела с ожидаемым (роль Технолог) ---
    var segs = location.pathname.split('/').filter(Boolean);
    var last = segs[segs.length - 1] || '';
    var folder = /\.php$/.test(last) ? (segs[segs.length - 2] || '') : last;
    var expectedTabs = {
      'calculation': ['Отгружено', 'Ждёт отгрузки', 'Производят', 'В работе', 'Расчёты', 'Черновики', 'Корзина'],
      'pack':        ['Производят', 'Упаковка', 'Ждёт отгрузки', 'Отгружено'],
      'roll':        ['Рулоны', 'Паллеты', 'Раскроили', 'Сработанная пленка'],
      'pallet':      ['Рулоны', 'Паллеты', 'Раскроили', 'Сработанная пленка'],
      'cut_source':  ['Рулоны', 'Паллеты', 'Раскроили', 'Сработанная пленка'],
      'utilized':    ['Рулоны', 'Паллеты', 'Раскроили', 'Сработанная пленка'],
      'user':        ['Сотрудники', 'Поставщики', 'Пленка', 'Нормы', 'Курсы валют', 'План'],
      'supplier':    ['Сотрудники', 'Поставщики', 'Пленка', 'Нормы', 'Курсы валют', 'План'],
      'admin':       ['Сотрудники', 'Поставщики', 'Пленка', 'Нормы', 'Курсы валют', 'План']
    };
    var gotTabs = [].slice.call(document.querySelectorAll('.flexim-header-menu__item')).map(function (t) { return t.textContent.trim(); });
    if (expectedTabs[folder]) {
      var exp = expectedTabs[folder];
      var same = gotTabs.length === exp.length && exp.every(function (l) { return gotTabs.indexOf(l) !== -1; });
      add('Состав хэдера совпадает с разделом', same, gotTabs.join(' · '));
    } else {
      add('Табы хэдера присутствуют', gotTabs.length > 0, gotTabs.length ? gotTabs.join(' · ') : 'нет табов');
    }
    add('Активный таб подсвечен', !!document.querySelector('.flexim-header-menu__item--active'));

    // --- Контент не тронут: нет DS-обёртки и body не заморожен ---
    var noWrap = !document.querySelector('.app-content, .app-shell');
    var notFrozen = cs(document.body).overflow !== 'hidden';
    add('Контент не обёрнут / не заморожен', noWrap && notFrozen, (noWrap ? 'без обёртки' : 'ЕСТЬ обёртка') + ', body overflow=' + cs(document.body).overflow);

    var pass = checks.filter(function (c) { return c.ok; }).length;
    var all = checks.length;
    var okAll = pass === all;

    console.group('%cDS-хэдер · самопроверка ' + pass + '/' + all, 'font-weight:bold;color:' + (okAll ? '#2e7d32' : '#c62828'));
    checks.forEach(function (c) { console.log((c.ok ? '✅' : '❌') + ' ' + c.name + (c.got ? '  (' + c.got + ')' : '')); });
    console.groupEnd();

    var badge = document.createElement('div');
    badge.style.cssText =
      'position:fixed;right:12px;bottom:12px;z-index:99999;' +
      'font:600 12px/1.4 -apple-system,sans-serif;color:#fff;padding:8px 10px;border-radius:8px;' +
      'background:' + (okAll ? '#2e7d32' : '#c62828') + ';box-shadow:0 4px 12px rgba(0,0,0,.25);' +
      'cursor:pointer;max-width:320px;';
    var head = function () { return 'DS-хэдер: ' + pass + '/' + all + (okAll ? ' ✓ как в дизайне' : ' ✗ есть отличия'); };
    badge.textContent = head();
    var open = false;
    badge.addEventListener('click', function () {
      open = !open;
      if (!open) { badge.textContent = head(); return; }
      badge.innerHTML = head() + '<hr style="border:0;border-top:1px solid rgba(255,255,255,.3);margin:6px 0">' +
        checks.map(function (c) { return (c.ok ? '✅' : '❌') + ' ' + c.name + (c.got ? ' — ' + c.got : ''); }).join('<br>');
    });
    document.body.appendChild(badge);
  });
})();
