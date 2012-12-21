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

<? for ($i = 1; $i <= $columns; $i++) { ?>

	<div class="ccm-layout-column" id="ccm-layout-column-<?=$i?>">
		<div class="ccm-layout-column-inner">
			<? 
			$as = new SubArea("Column $i", $a);
			$as->display($c);
			?>
		</div>
	</div>

<? } ?>

</div>