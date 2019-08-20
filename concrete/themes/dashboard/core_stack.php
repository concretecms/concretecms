<?php
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Area\Area;
use Concrete\Core\Legacy\Loader;
$this->inc('elements/header.php'); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($c->getCollectionName())?>

<?php

$a = new Area(STACKS_AREA_NAME);
$a->display($c);
?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>


<?php $this->inc('elements/footer.php'); ?>