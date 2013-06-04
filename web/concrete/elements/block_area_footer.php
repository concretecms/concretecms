<? 
defined('C5_EXECUTE') or die("Access Denied.");
?>

<? 

// simple file that controls the adding of blocks.

// $blockTypes is an array using the btID as the key and btHandle as the value.
// It is defined within Area->_getAreaAddBlocks(), which then calls a 
// function in Content to include the file

// note, we're also passed an area & collection object from the original function

$arHandle = $a->getAreaHandle();
$c = $a->getAreaCollectionObject();
$cID = $c->getCollectionID();
$u = new User();
$ap = new Permissions($a);
$cp = new Permissions($c);
$class = 'ccm-area-footer';

?>
</div>

<div class="<?=$class?> ccm-ui">

<div class="ccm-area-footer-handle" id="area-menu-footer-<?=$a->getAreaID()?>"><span><i class="icon-share-alt"></i> <?=$a->getAreaDisplayName()?></span></div>

<div class="popover fade" id="area-menu-a<?=$a->getAreaID()?>">
	<div class="arrow"></div>
	<div class="popover-inner">
	<ul class="dropdown-menu">
	<? if ($ap->canAddBlockToArea()) { ?>
		<li data-list-item="block_limit_row"><a dialog-title="<?=t('Add New Block')?>" class="dialog-launch" dialog-modal="false" dialog-width="660" dialog-height="430" id="menuAddNewBlock<?=$a->getAreaID()?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/add_block?cID=<?=$c->getCollectionID()?>&arHandle=<?=urlencode($a->getAreaHandle())?>"><?=t("Add New Block")?></a></li>
		<li data-list-item="block_limit_row"><a dialog-title="<?=t('Paste from Clipboard')?>" class="dialog-launch" dialog-modal="false" dialog-width="550" dialog-height="380" id="menuAddPaste<?=$a->getAreaID()?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup.php?cID=<?=$c->getCollectionID()?>&arHandle=<?=urlencode($a->getAreaHandle())?>&atask=paste"><?=t("Paste from Clipboard")?></a></li>
	<? } ?>
	<? if ($ap->canAddStacks()) { ?>
		<li data-list-item="block_limit_row"><a dialog-title="<?=t('Add from Stack')?>" class="dialog-launch" dialog-modal="false" dialog-width="550" dialog-height="380" id="menuAddNewStack<?=$a->getAreaID()?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup.php?cID=<?=$c->getCollectionID()?>&arHandle=<?=urlencode($a->getAreaHandle())?>&atask=add_from_stack"><?=t("Add Stack")?></a></li>
	<? } ?>
	<? if ($ap->canAddBlockToArea() || $ap->canAddStacks()) { ?>
		<li data-list-item="block_limit_row" class="divider"></li>
	<? } ?>

	<?
		$showAreaDesign = ($ap->canEditAreaDesign() && ENABLE_CUSTOM_DESIGN == true);
		$showAreaLayouts = ($ap->canAddLayoutToArea() && ENABLE_AREA_LAYOUTS == true);		
		$canEditAreaPermissions = ($ap->canEditAreaPermissions() && PERMISSIONS_MODEL != 'simple' && (!$a->isGlobalArea()));	
	?>

	<? if ($showAreaDesign || $showAreaLayouts) { ?>
		<? if ($showAreaDesign) { ?>
			<li><a dialog-title="<?=t('Custom Style')?>" class="dialog-launch" dialog-modal="false" dialog-width="475" dialog-height="500" id="menuAreaStyle<?=$a->getAreaID()?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup.php?cID=<?=$c->getCollectionID()?>&arHandle=<?=urlencode($a->getAreaHandle())?>&atask=design"><?=t("Edit Area Design")?></a></li>		
		<? } ?>
		<? if ($showAreaLayouts) { ?>
			<? $areaLayoutBT = BlockType::getByHandle('core_area_layout'); ?>
			<? $params = 'false'; ?>

			<? if ($a->getAreaGridColumnSpan() > 0) {
				$params = '{arGridColumnSpan: ' . $a->getAreaGridColumnSpan() . '}';
			} ?>
			<li><a dialog-title="<?=t('Add Layout')?>" onclick="CCMInlineEditMode.loadAdd(<?=$c->getCollectionID()?>, '<?=htmlspecialchars($arHandle)?>', <?=$a->getAreaID()?>, <?=$areaLayoutBT->getBlockTypeID()?>, <?=$params?>)" id="menuLayout<?=$a->getAreaID()?>" href="javascript:void(0)"><?=t("Add Layout")?></a></li>		
		<? } ?>
		<? if ($canEditAreaPermissions) { ?>
			<li class="divider"></li>
		<? } ?>
	<? } ?>

	<? if ($canEditAreaPermissions) { ?>
		<li><a dialog-title="<?=t('Area Permissions')?>" class="dialog-launch" dialog-modal="false" dialog-width="425" dialog-height="430" id="menuAreaStyle<?=$a->getAreaID()?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup.php?cID=<?=$c->getCollectionID()?>&arHandle=<?=urlencode($a->getAreaHandle())?>&atask=groups"><?=t("Permissions")?></a></li>		
	<? } ?>

	<? 
	if ($a instanceof SubArea) {
		$bt = BlockType::getByHandle(BLOCK_HANDLE_LAYOUT_PROXY);
		$ax = $a->getSubAreaParentPermissionsObject();
		$axp = new Permissions($ax);
		if ($axp->canAddBlockToArea($bt)) { 
			$bx = $a->getSubAreaBlockObject();
			if (is_object($bx) && !$bx->isError()) { ?>
				<li class="divider"></li>
				<li><a href="javascript:void(0)" data-menu-action="edit_inline" data-menu-edit-params="<?=$params?>"><?=t("Edit Container Layout")?></a></li>		
			<? } ?>
		<? }
	} ?>
	</ul>
	</div>
</div>
</div>
</div>