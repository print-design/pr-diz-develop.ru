<?php
/*
 * DS-хэдер раздела «План» (develop). Состав табов — 1:1 со старым header_plan.php:
 * типы работ (WORKS). Поиска в разделе нет.
 */
ob_start();
foreach(WORKS as $work):
    $active = ($work_id == $work) ? ' flexim-header-menu__item--active' : '';
    if(!(GetUserId() == CUTTER_SOMA && $work !== WORK_CUTTING)): // ВРЕМЕННО (как в header_plan.php)
?>
<a class="flexim-header-menu__item<?=$active ?>" href="<?= BuildQueryAddRemove('work_id', $work, 'machine_id') ?>"><?=WORK_NAMES[$work] ?></a>
<?php
    endif;
endforeach;
$ds_tabs = ob_get_clean();
$ds_search_html = ''; // в плане поиска нет
include '_ds_header.php';
