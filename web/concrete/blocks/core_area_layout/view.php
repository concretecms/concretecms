<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$a = $b->getBlockAreaObject();
	$c = Page::getCurrentPage();
?>

<? if ($c->isEditMode()) {
	$bp = new Permissions($b);
	if ($bp->canEditBlock()) { ?>

		<div class="ccm-area-layout-control-bar" data-handle="block-menu-b<?=$b->getBlockID()?>-<?=$a->getAreaID()?>"></div>

	<? } ?>

<? } ?>


<div class="ccm-layout-column-wrapper" id="ccm-layout-column-wrapper-<?=$bID?>">

<? foreach($columns as $col) { ?>
	<div class="ccm-layout-column" id="ccm-layout-column-<?=$col->getAreaLayoutColumnIndex()?>">
		<div class="ccm-layout-column-inner">
			<? 
			$as = new SubArea($col->getAreaLayoutColumnIndex(), $a);
			$as->display($c);
			?>
		</div>
	</div>

<? } ?>

</div>