<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$btl = new BlockTypeList();
$blockTypes = $btl->getBlockTypeList();
$handles = '';
$ap = new Permissions($a);
foreach($blockTypes as $bt) {
	if ($ap->canAddBlockToArea($bt)) {
		$handles .= $bt->getBlockTypeHandle() . ' ';
	}
}
?>
<div id="a<?=$a->getAreaID()?>" data-accepts-block-types="<?=trim($handles)?>" data-aID="<?=$a->getAreaID()?>" data-cID="<?=$a->getCollectionID()?>" data-area-handle="<?=$a->getAreaHandle()?>" data-menu-disable-highlight="true" data-menu="area-menu-a<?=$a->getAreaID()?>" data-menu-handle="area-menu-footer-<?=$a->getAreaID()?>" class="ccm-area">