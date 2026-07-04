<?php
/*
 * Общий DS-хэдер (develop), НЕИНВАЗИВНЫЙ: рейка (position:fixed слева) + верхняя
 * панель (position:sticky сверху). Контент НЕ оборачивается и НЕ меняется —
 * течёт как в оригинале (отступ body под рейку остаётся от main.css).
 * Раздел задаёт $ds_tabs (HTML табов) и $ds_search_html (HTML поиска или ''),
 * затем include '_ds_header.php'. $ds_header — маркер для footer (самопроверка).
 * Закрывать в footer ничего не нужно — обёрток нет.
 */
$ds_header = true;

// Активный раздел рейки (как в left_bar.php)
$__subs   = mb_split("/", $_SERVER['PHP_SELF']);
$__folder = count($__subs) > 1 ? $__subs[count($__subs) - 2] : '';
$__a = function ($cond) { return $cond ? ' flexim-nav-rail__tab--active' : ''; };
$a_zakaz = $__a(in_array($__folder, array('calculation', 'techmap', 'schedule')));
$a_sklad = $__a(in_array($__folder, array('roll', 'pallet', 'cut_source', 'utilized', 'rational_cut')));
$a_plan  = $__a($__folder == 'plan');
$a_cut   = $__a($__folder == 'cut');
$a_pack  = $__a($__folder == 'pack');
$a_admin = $__a(in_array($__folder, array('user', 'supplier', 'admin')));
$a_impr  = $__a($__folder == 'improvement');
?>
<!-- ЛЕВАЯ РЕЙКА (fixed) — роли/активный раздел как в left_bar.php -->
<aside class="flexim-nav-rail" aria-label="Левое меню">
  <a href="<?=APPLICATION ?>/" class="flexim-logo__mark" aria-label="На главную"></a>
  <nav class="flexim-nav-rail__tabs">
    <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))): ?>
    <a href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIPPED, array("page", "order")) ?>" class="flexim-nav-rail__tab<?=$a_zakaz ?>" aria-label="Заказы"><span data-flexim-icon="time" data-size="24" aria-hidden="true"></span></a>
    <?php endif;
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER]))): ?>
    <a href="<?=APPLICATION ?>/roll/" class="flexim-nav-rail__tab<?=$a_sklad ?>" aria-label="Склад"><span data-flexim-icon="box" data-size="24" aria-hidden="true"></span></a>
    <?php endif;
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT], ROLE_NAMES[ROLE_COLORIST]))): ?>
    <a href="<?=APPLICATION ?>/plan/" class="flexim-nav-rail__tab<?=$a_plan ?>" aria-label="План"><span data-flexim-icon="calendar" data-size="24" aria-hidden="true"></span></a>
    <?php endif;
    if(IsInRole(CUTTER_USERS) || IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_LAM_HEAD]))): ?>
    <a href="<?=APPLICATION ?>/cut/<?= IsInRole(CUTTER_USERS) ? "" : "?machine_id=".CUTTER_1 ?>" class="flexim-nav-rail__tab<?=$a_cut ?>" aria-label="Резка"><span data-flexim-icon="factory" data-size="24" aria-hidden="true"></span></a>
    <?php endif;
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT]))): ?>
    <a href="<?=APPLICATION ?>/pack/" class="flexim-nav-rail__tab<?=$a_pack ?>" aria-label="Упаковка"><span data-flexim-icon="loader-machine" data-size="24" aria-hidden="true"></span></a>
    <?php endif;
    if(IsInRole(ROLE_NAMES[ROLE_TECHNOLOGIST])): ?>
    <a href="<?=APPLICATION ?>/user/" class="flexim-nav-rail__tab<?=$a_admin ?>" aria-label="Админка"><span data-flexim-icon="settings" data-size="24" aria-hidden="true"></span></a>
    <?php elseif(IsInRole(array(ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD]))): ?>
    <a href="<?=APPLICATION ?>/admin/plan_employees.php" class="flexim-nav-rail__tab<?=$a_admin ?>" aria-label="Админка"><span data-flexim-icon="settings" data-size="24" aria-hidden="true"></span></a>
    <?php elseif(IsInRole(ROLE_NAMES[ROLE_MANAGER_SENIOR])): ?>
    <a href="<?=APPLICATION ?>/supplier/film.php" class="flexim-nav-rail__tab<?=$a_admin ?>" aria-label="Админка"><span data-flexim-icon="settings" data-size="24" aria-hidden="true"></span></a>
    <?php endif;
    if(IsInRole(ROLE_NAMES[ROLE_MANAGER_SENIOR]) || IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD]))): ?>
    <a href="<?=APPLICATION ?>/improvement/" class="flexim-nav-rail__tab<?=$a_impr ?>" aria-label="Предложения по улучшению"><span data-flexim-icon="like" data-size="24" aria-hidden="true"></span></a>
    <?php endif; ?>
  </nav>
</aside>

<!-- ВЕРХНЯЯ ПАНЕЛЬ (sticky): табы раздела + поиск + чип пользователя -->
<div class="app-topbar">
  <div class="flexim-header-menu">
    <nav class="flexim-header-menu__nav"><?= isset($ds_tabs) ? $ds_tabs : '' ?></nav>
    <div class="app-topbar__right">
      <?= isset($ds_search_html) ? $ds_search_html : '' ?>
      <?php if(!empty(filter_input(INPUT_COOKIE, USERNAME))):
        $u_last = filter_input(INPUT_COOKIE, LAST_NAME);
        $u_first = filter_input(INPUT_COOKIE, FIRST_NAME);
        $u_role = filter_input(INPUT_COOKIE, ROLE_LOCAL);
        $u_initials = mb_strtoupper((mb_strlen($u_last) ? mb_substr($u_last, 0, 1) : '').(mb_strlen($u_first) ? mb_substr($u_first, 0, 1) : ''));
      ?>
      <div class="flexim-user-chip dropdown" id="nav-user">
        <span class="flexim-avatar flexim-avatar--m flexim-avatar--initials" title="<?=$u_last.' '.$u_first ?>"><?=$u_initials ?></span>
        <span class="flexim-user-chip__name" data-toggle="dropdown" id="navbardrop" style="cursor: pointer;">
          <span class="flexim-user-chip__title"><?=$u_last.' '.$u_first ?></span>
          <span class="flexim-user-chip__role"><?=$u_role ?></span>
        </span>
        <div class="dropdown-menu dropdown-menu-right" id="user-dropdown">
          <a href="<?=APPLICATION ?>/personal/" class="dropdown-item"><i class="fas fa-user"></i>&nbsp;Мои настройки</a>
          <form method="post"><button type="submit" class="dropdown-item" id="logout_submit" name="logout_submit"><i class="fas fa-sign-out-alt"></i>&nbsp;Выход</button></form>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
