<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$btl = new BlockTypeList();
$blockTypes = $btl->get();
$handles = '';
$ap = new Permissions($a);
$class = 'ccm-area';
if ($a->isGlobalArea()) {
	$class .= ' ccm-global-area';
}

$canAddGathering = false;

foreach($blockTypes as $bt) {
	if ($ap->canAddBlockToArea($bt)) {
		$handles .= $bt->getBlockTypeHandle() . ' ';
		if ($bt->getBlockTypeHandle() == BLOCK_HANDLE_GATHERING) {
			$canAddGathering = true;
		}
	}
}

if ($ap->canAddLayout()) {
	$handles .= BLOCK_HANDLE_LAYOUT_PROXY . ' ';
}

if ($canAddGathering) {
	$handles .= BLOCK_HANDLE_GATHERING_ITEM_PROXY . ' ';
}

$c = Page::getCurrentPage();
if ($c->isMasterCollection()) {
	$handles .= BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY . ' ';
}
?>
<div id="a<?=$a->getAreaID()?>" data-maximum-blocks="<?=$a->getMaximumBlocks()?>"
data-accepts-block-types="<?=trim($handles)?>" 
data-area-id="<?=$a->getAreaID()?>" 
data-cID="<?=$a->getCollectionID()?>" 
data-area-handle="<?=$a->getAreaHandle()?>" 
data-area-menu-handle="<?=$a->getAreaID()?>"
data-launch-area-menu="area-menu-a<?=$a->getAreaID()?>" 
class="<?=$class?>">

<? unset($class); ?>

<div class="ccm-area-block-list">
