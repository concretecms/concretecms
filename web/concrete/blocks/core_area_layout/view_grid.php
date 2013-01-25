<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$a = $b->getBlockAreaObject();
	$c = Page::getCurrentPage();
	$pt = $c->getCollectionThemeObject();
	$gf = $pt->getThemeGridFrameworkObject();
?>

<? if ($c->isEditMode()) {
	$bp = new Permissions($b);
	if ($bp->canEditBlock()) { ?>
		<div class="ccm-area-layout-control-bar" data-handle="block-menu-b<?=$b->getBlockID()?>-<?=$a->getAreaID()?>"></div>
	<? } ?>

<? } ?>

<?=$gf->getPageThemeGridFrameworkRowStartHTML()?>

<? foreach($columns as $col) { ?>
	<? if ($col->getAreaLayoutColumnOffset() > 0 && (!$gf->hasPageThemeGridFrameworkOffsetClasses())) { ?>
		<div class="ccm-theme-grid-offset-column <?=$col->getAreaLayoutColumnOffsetClass()?>"></div>
	<? } ?>
	<div class="<?=$col->getAreaLayoutColumnClass()?><? if ($gf->hasPageThemeGridFrameworkOffsetClasses() && $col->getAreaLayoutColumnOffset()) { ?> <?=$col->getAreaLayoutColumnOffsetClass()?><? } ?>"><? 
		$col->display();
	?></div>

<? } ?>

<?=$gf->getPageThemeGridFrameworkRowEndHTML()?>