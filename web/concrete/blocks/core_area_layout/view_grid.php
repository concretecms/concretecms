<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$a = $b->getBlockAreaObject();
?>

<?=$gf->getPageThemeGridFrameworkRowStartHTML()?>

<? foreach($columns as $col) { ?>
	<? if ($col->getAreaLayoutColumnOffset() > 0 && (!$gf->hasPageThemeGridFrameworkOffsetClasses())) { ?>
		<div class="<?=$col->getAreaLayoutColumnOffsetClass()?> ccm-theme-grid-offset-column"></div>
	<? } ?>
	<div class="<?=$col->getAreaLayoutColumnClass()?><? if ($gf->hasPageThemeGridFrameworkOffsetClasses() && $col->getAreaLayoutColumnOffset()) { ?> <?=$col->getAreaLayoutColumnOffsetClass()?><? } ?>"><? 
		$col->display();
	?></div>

<? } ?>

<?=$gf->getPageThemeGridFrameworkRowEndHTML()?>