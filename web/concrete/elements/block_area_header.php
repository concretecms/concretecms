<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$btl = new BlockTypeList();
$blockTypes = $btl->getBlockTypeList();
$handles = '';
$ap = new Permissions($a);
$class = 'ccm-area';
if ($a->isGlobalArea()) {
	$class .= ' ccm-global-area';
}

foreach($blockTypes as $bt) {
	if ($ap->canAddBlockToArea($bt)) {
		$handles .= $bt->getBlockTypeHandle() . ' ';
	}
}
?>
<div id="a<?=$a->getAreaID()?>" data-total-blocks="<?=$a->getTotalBlocksInAreaEditMode()?>" data-accepts-block-types="<?=trim($handles)?>" data-area-id="<?=$a->getAreaID()?>" data-cID="<?=$a->getCollectionID()?>" data-area-handle="<?=$a->getAreaHandle()?>" data-menu-disable-highlight="true" data-menu="area-menu-a<?=$a->getAreaID()?>" data-menu-handle="area-menu-footer-<?=$a->getAreaID()?>" class="<?=$class?>">

<? unset($class); ?>