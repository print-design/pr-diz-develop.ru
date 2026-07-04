<?php
/*
 * DS-хэдер раздела «Упаковка» (develop). Состав табов и поиск — 1:1 со старым
 * include/header_pack.php: Производят · Упаковка · Ждёт отгрузки · Отгружено + поиск.
 * Только оформление меняется на DS; серверная логика та же.
 */

// Активный таб (как в header_pack.php)
$production = $pack = $ship = $shipped = '';
$sid = filter_input(INPUT_GET, 'status_id');
if(empty($sid) && !empty($calculation)) {
    $sid = $calculation->status_id;
}
if($sid == ORDER_STATUS_PACK_READY)      $pack     = ' flexim-header-menu__item--active';
elseif($sid == ORDER_STATUS_SHIP_READY)  $ship     = ' flexim-header-menu__item--active';
elseif($sid == ORDER_STATUS_SHIPPED)     $shipped  = ' flexim-header-menu__item--active';
else                                     $production = ' flexim-header-menu__item--active';

// --- Табы ---
ob_start(); ?>
<a class="flexim-header-menu__item<?=$production ?>" href="<?= APPLICATION."/pack/" ?>">Производят</a>
<a class="flexim-header-menu__item<?=$pack ?>" href="<?= APPLICATION."/pack/?status_id=".ORDER_STATUS_PACK_READY ?>">Упаковка</a>
<a class="flexim-header-menu__item<?=$ship ?>" href="<?= APPLICATION."/pack/?status_id=".ORDER_STATUS_SHIP_READY ?>">Ждёт отгрузки</a>
<a class="flexim-header-menu__item<?=$shipped ?>" href="<?= APPLICATION."/pack/?status_id=".ORDER_STATUS_SHIPPED ?>">Отгружено</a>
<?php $ds_tabs = ob_get_clean();

// --- Поиск (в разделе есть find.php) ---
ob_start();
if(LoggedIn()):
    $find_value = trim(filter_input(INPUT_GET, 'find') ?? '');
    $has_find = $find_value !== '';
    $sid_get = filter_input(INPUT_GET, 'status_id');
    $reset_href = APPLICATION.'/pack/'.($sid_get !== null ? '?status_id='.$sid_get : '');
?>
<form class="flexim-search-row flexim-search-row--fill" method="get" action="<?=APPLICATION.'/pack/' ?>">
  <div class="flexim-search flexim-search--fill">
    <div class="flexim-search__field" onclick="if(!event.target.closest('a,button')) this.querySelector('.flexim-search__input').focus();">
      <span class="flexim-search__icon" data-flexim-icon="search" data-size="24" aria-hidden="true"></span>
      <div class="flexim-search__chips">
        <?php if($has_find): ?>
        <span class="flexim-chip-tag flexim-chip-tag--s"><?=htmlentities($find_value) ?> <a href="<?=$reset_href ?>" class="flexim-chip-dismiss" aria-label="Убрать поиск" onclick="event.stopPropagation();"><span data-flexim-icon="x-small" data-size="16" aria-hidden="true"></span></a></span>
        <?php endif; ?>
      </div>
      <input class="flexim-search__input" type="text" name="find" placeholder="<?= $has_find ? '' : 'Поиск по расчётам' ?>">
      <?php if($sid_get !== null): ?><input type="hidden" name="status_id" value="<?=$sid_get ?>"><?php endif; ?>
      <button type="submit" class="d-none" aria-hidden="true"></button>
    </div>
  </div>
</form>
<?php endif;
$ds_search_html = ob_get_clean();

include '_ds_header.php';
