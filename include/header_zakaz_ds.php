<?php
/*
 * DS-хэдер раздела «Заказы» (develop). Состав табов и поиск — 1:1 со старым
 * include/header_zakaz.php. Оформление — DS через общий _ds_header.php.
 */

// Активный таб-статус (как в header_zakaz.php)
if(empty($status_id) && !empty($calculation)) {
    $status_id = $calculation->status_id;
}
$tab_shipped = $tab_ship_ready = $tab_production = $tab_calculation = $tab_not_in_work = $tab_draft = $tab_trash = '';
if($status_id == ORDER_STATUS_TRASH)                                                                    $tab_trash       = ' flexim-header-menu__item--active';
elseif($status_id == ORDER_STATUS_DRAFT)                                                                $tab_draft       = ' flexim-header-menu__item--active';
elseif(in_array($status_id, ORDER_STATUSES_NOT_IN_WORK) || $status_id == ORDER_STATUS_NOT_IN_WORK)     $tab_not_in_work = ' flexim-header-menu__item--active';
elseif(in_array($status_id, ORDER_STATUSES_IN_PRODUCTION) || $status_id == ORDER_STATUS_IN_PRODUCTION) $tab_production  = ' flexim-header-menu__item--active';
elseif($status_id == ORDER_STATUS_SHIP_READY)                                                          $tab_ship_ready  = ' flexim-header-menu__item--active';
elseif($status_id == ORDER_STATUS_SHIPPED)                                                             $tab_shipped     = ' flexim-header-menu__item--active';
else                                                                                                   $tab_calculation = ' flexim-header-menu__item--active';

// --- Табы ---
ob_start();
if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))):
?>
<a class="flexim-header-menu__item<?=$tab_shipped ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIPPED, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_SHIPPED] ?></a>
<a class="flexim-header-menu__item<?=$tab_ship_ready ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_SHIP_READY, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_SHIP_READY] ?></a>
<a class="flexim-header-menu__item<?=$tab_production ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_IN_PRODUCTION, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_IN_PRODUCTION] ?></a>
<a class="flexim-header-menu__item<?=$tab_calculation ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryRemoveArray(array("status", "page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_IN_WORK] ?></a>
<a class="flexim-header-menu__item<?=$tab_not_in_work ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_NOT_IN_WORK, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_NOT_IN_WORK] ?></a>
<a class="flexim-header-menu__item<?=$tab_draft ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_DRAFT, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_DRAFT] ?></a>
<a class="flexim-header-menu__item<?=$tab_trash ?>" href="<?=APPLICATION ?>/calculation/<?= BuildQueryAddRemoveArray("status", ORDER_STATUS_TRASH, array("page", "order")) ?>"><?=ORDER_STATUS_TITLES[ORDER_STATUS_TRASH] ?></a>
<?php endif;
$ds_tabs = ob_get_clean();

// --- Поиск (в разделе есть find.php) ---
ob_start();
if(LoggedIn()):
    $find_value = trim(filter_input(INPUT_GET, 'find') ?? '');
    $has_find = $find_value !== '';
    $status_get = filter_input(INPUT_GET, 'status');
    $reset_href = APPLICATION.'/calculation/'.($status_get !== null ? '?status='.$status_get : '');
?>
<form class="flexim-search-row flexim-search-row--fill" method="get" action="<?=APPLICATION.'/calculation/' ?>">
  <div class="flexim-search flexim-search--fill">
    <div class="flexim-search__field" onclick="if(!event.target.closest('a,button')) this.querySelector('.flexim-search__input').focus();">
      <span class="flexim-search__icon" data-flexim-icon="search" data-size="24" aria-hidden="true"></span>
      <div class="flexim-search__chips">
        <?php if($has_find): ?>
        <span class="flexim-chip-tag flexim-chip-tag--s"><?=htmlentities($find_value) ?> <a href="<?=$reset_href ?>" class="flexim-chip-dismiss" aria-label="Убрать поиск" onclick="event.stopPropagation();"><span data-flexim-icon="x-small" data-size="16" aria-hidden="true"></span></a></span>
        <?php endif; ?>
      </div>
      <input class="flexim-search__input" type="text" name="find" placeholder="<?= $has_find ? '' : 'Поиск по расчётам' ?>">
      <?php if($status_get !== null): ?><input type="hidden" name="status" value="<?= $status_get ?>"><?php endif; ?>
      <button type="submit" class="d-none" aria-hidden="true"></button>
    </div>
  </div>
</form>
<?php endif;
$ds_search_html = ob_get_clean();

include '_ds_header.php';
