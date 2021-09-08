<?php
defined('C5_EXECUTE') or die("Access Denied.");

$view->inc('elements/header.php');

$a = new Area('Page Header');
$a->enableGridContainer();
$a->display($c);

$a = new Area('Main');
$a->enableGridContainer();
$a->display($c);

$a = new Area('Page Footer');
$a->enableGridContainer();
$a->display($c);

$view->inc('elements/footer.php');
