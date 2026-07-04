<?php
/*
 * DS-хэдер раздела «Админка» (develop). Состав табов — 1:1 со старым header_admin.php:
 * Сотрудники · Поставщики · Пленка · Нормы · Курсы валют · План (по ролям). Поиска нет.
 */
$__subs = mb_split("/", $_SERVER['PHP_SELF']);
$__cnt = count($__subs);
$folder = $__cnt > 1 ? $__subs[$__cnt - 2] : '';
$file   = $__cnt > 1 ? $__subs[$__cnt - 1] : '';
$A = ' flexim-header-menu__item--active';
$user_a = $supplier_a = $film_a = $norm_a = $currency_a = $plan_a = '';
if($folder == 'user')                                                   $user_a = $A;
elseif($folder == 'supplier' && $file != 'film.php')                    $supplier_a = $A;
elseif($file == 'film.php')                                             $film_a = $A;
elseif($file == 'currency.php')                                         $currency_a = $A;
elseif($file == 'plan_employees.php' || $file == 'plan_employees_create.php') $plan_a = $A;
elseif($folder == 'admin')                                              $norm_a = $A;

ob_start(); ?>
<?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST]))): ?>
<a class="flexim-header-menu__item<?=$user_a ?>" href="<?=APPLICATION ?>/user/">Сотрудники</a>
<a class="flexim-header-menu__item<?=$supplier_a ?>" href="<?=APPLICATION ?>/supplier/">Поставщики</a>
<?php endif; ?>
<?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR]))): ?>
<a class="flexim-header-menu__item<?=$film_a ?>" href="<?=APPLICATION ?>/supplier/film.php">Пленка</a>
<a class="flexim-header-menu__item<?=$norm_a ?>" href="<?=APPLICATION ?>/admin/machine.php<?= BuildQuery('machine_id', 4) ?>">Нормы</a>
<a class="flexim-header-menu__item<?=$currency_a ?>" href="<?=APPLICATION ?>/admin/currency.php">Курсы валют</a>
<?php endif; ?>
<?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD]))): ?>
<a class="flexim-header-menu__item<?=$plan_a ?>" href="<?=APPLICATION ?>/admin/plan_employees.php">План</a>
<?php endif; ?>
<?php
$ds_tabs = ob_get_clean();
$ds_search_html = ''; // в админке поиска нет
include '_ds_header.php';
