<?
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($c->getCollectionName())?>

<? 

$a = new Area(STACKS_AREA_NAME);
$a->display($c); 
?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>


<? $this->inc('elements/footer.php'); ?>