<?php
/*
 * DS-хэдер раздела «Склад» (develop). Состав табов — 1:1 со старым header_sklad.php:
 * Рулоны · Паллеты · Раскроили · Сработанная пленка + поиск «Поиск по складу».
 * (Скрытый rational_cut и dev-метка ТЕСТОВАЯ/РАЗРАБОТКА в DS-хэдер не переносим —
 *  это не пункты меню.)
 */
$__subs = mb_split("/", $_SERVER['PHP_SELF']);
$__cnt = count($__subs);
$folder = $__cnt > 1 ? $__subs[$__cnt - 2] : '';
$file   = $__cnt > 1 ? $__subs[$__cnt - 1] : '';
$A = ' flexim-header-menu__item--active';
$rolls = $pallets = $cut_sources = $utilized = '';
if($folder == 'roll')            $rolls = $A;
elseif($folder == 'pallet')      $pallets = $A;
elseif($folder == 'cut_source')  $cut_sources = $A;
elseif($folder == 'utilized')    $utilized = $A;
// Спец-случаи страницы рулона (как в header_sklad.php)
if($folder == 'roll' && $file == 'roll.php') {
    if(isset($status_id) && $status_id == ROLL_STATUS_UTILIZED)   { $rolls = ''; $cut_sources = ''; $utilized = $A; }
    elseif(isset($status_id) && $status_id == ROLL_STATUS_CUT)    { $rolls = ''; $cut_sources = $A; $utilized = ''; }
}
if($folder == 'pallet' && $file == 'roll.php') {
    if(isset($status_id) && $status_id == ROLL_STATUS_UTILIZED)   { $pallets = ''; $cut_sources = ''; $utilized = $A; }
    elseif(isset($status_id) && $status_id == ROLL_STATUS_CUT)    { $pallets = ''; $cut_sources = $A; $utilized = ''; }
}

// --- Табы ---
ob_start();
if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))):
?>
<a class="flexim-header-menu__item<?=$rolls ?>" href="<?=APPLICATION ?>/roll/<?= BuildQueryRemoveArray(array('page', 'id', 'order')) ?>">Рулоны</a>
<a class="flexim-header-menu__item<?=$pallets ?>" href="<?=APPLICATION ?>/pallet/<?= BuildQueryRemoveArray(array('page', 'id', 'order')) ?>">Паллеты</a>
<a class="flexim-header-menu__item<?=$cut_sources ?>" href="<?=APPLICATION ?>/cut_source/<?= BuildQueryRemoveArray(array('page', 'id', 'order')) ?>">Раскроили</a>
<a class="flexim-header-menu__item<?=$utilized ?>" href="<?=APPLICATION ?>/utilized/<?= BuildQueryRemoveArray(array('page', 'id', 'order')) ?>">Сработанная пленка</a>
<?php endif;
$ds_tabs = ob_get_clean();

// --- Поиск (в папках склада есть find.php; форма шлёт find в текущий раздел) ---
ob_start();
if(LoggedIn() && file_exists('find.php')):
    $find_value = trim(filter_input(INPUT_GET, 'find') ?? '');
    $has_find = $find_value !== '';
    $sklad_action = APPLICATION.'/'.$folder.'/';
?>
<form class="flexim-search-row flexim-search-row--fill" method="get" action="<?=$sklad_action ?>">
  <div class="flexim-search flexim-search--fill">
    <div class="flexim-search__field" onclick="if(!event.target.closest('a,button')) this.querySelector('.flexim-search__input').focus();">
      <span class="flexim-search__icon" data-flexim-icon="search" data-size="24" aria-hidden="true"></span>
      <div class="flexim-search__chips">
        <?php if($has_find): ?>
        <span class="flexim-chip-tag flexim-chip-tag--s"><?=htmlentities($find_value) ?> <a href="<?=$sklad_action ?>" class="flexim-chip-dismiss" aria-label="Убрать поиск" onclick="event.stopPropagation();"><span data-flexim-icon="x-small" data-size="16" aria-hidden="true"></span></a></span>
        <?php endif; ?>
      </div>
      <input class="flexim-search__input" type="text" name="find" placeholder="<?= $has_find ? '' : 'Поиск по складу' ?>">
      <button type="submit" class="d-none" aria-hidden="true"></button>
    </div>
  </div>
</form>
<?php endif;
$ds_search_html = ob_get_clean();

include '_ds_header.php';
