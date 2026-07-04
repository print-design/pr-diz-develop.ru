<?php
/*
 * DS-хэдер раздела «Резка» (develop). Состав табов — 1:1 со старым header_cut.php:
 * либо станки (CUTTERS, кроме ATLAS), либо один таб для оператора-резчика.
 * Поиска в разделе нет.
 */
ob_start();
if(IsInRole(CUTTER_USERS)): ?>
<a class="flexim-header-menu__item flexim-header-menu__item--active" href="<?=APPLICATION.'/cut/' ?>"><?=filter_input(INPUT_COOKIE, ROLE_LOCAL) ?></a>
<?php
else:
    foreach(CUTTERS as $cutter):
        if($cutter != CUTTER_ATLAS):
            $active = (filter_input(INPUT_GET, 'machine_id') == $cutter) ? ' flexim-header-menu__item--active' : '';
?>
<a class="flexim-header-menu__item<?=$active ?>" href="<?=APPLICATION.'/cut/?machine_id='.$cutter ?>"><?=CUTTER_NAMES[$cutter] ?></a>
<?php
        endif;
    endforeach;
endif;
$ds_tabs = ob_get_clean();
$ds_search_html = ''; // в резке поиска нет
include '_ds_header.php';
