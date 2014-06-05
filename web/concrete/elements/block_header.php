<? 
defined('C5_EXECUTE') or die("Access Denied.");
$c = $b->getBlockCollectionObject();
if (!is_object($a)) {
	$a = Area::get($c, $b->getAreaHandle());
}

$arHandle = $a->getAreaHandle();

$btw = BlockType::getByID($b->getBlockTypeID());
if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
	$class = 'ccm-block-edit-layout ccm-block-edit';
} else {
	$class = 'ccm-block-edit';
}

$class .= ($b->isAliasOfMasterCollection() || $b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) ? " ccm-block-alias" : "";

if ($b->getBlockTypeHandle() == BLOCK_HANDLE_STACK_PROXY) {
	$class .= ' ccm-block-stack ';
}

if ($a->isGlobalArea()) {
	$c = Page::getCurrentPage();
	$cID = $c->getCollectionID();
} else {
	$cID = $b->getBlockCollectionID();
	$c = $b->getBlockCollectionObject();
}
$editInline = false;
if ($btw->supportsInlineEdit()) {
	$editInline = true;
}
$btOriginal = $btw;
$bID = $b->getBlockID();
$aID = $a->getAreaID();
$heightPlus = 20;
$btHandle = $btw->getBlockTypeHandle();
if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
	$_bi = $b->getInstance();
	$_bo = Block::getByID($_bi->getOriginalBlockID());
	$btOriginal = BlockType::getByHandle($_bo->getBlockTypeHandle());
	$btHandle = $btOriginal->getBlockTypeHandle();
	$heightPlus = 80;
	if ($btOriginal->supportsInlineEdit()) {
		$editInline = true;
	}
}
$canDesign = ($p->canEditBlockDesign() && ENABLE_CUSTOM_DESIGN == true);
$canModifyGroups = ($p->canEditBlockPermissions() && PERMISSIONS_MODEL != 'simple' && (!$a->isGlobalArea()));
$canScheduleGuestAccess = (PERMISSIONS_MODEL != 'simple' && $p->canGuestsViewThisBlock() && $p->canScheduleGuestAccess() && (!$a->isGlobalArea()));
$canAliasBlockOut = ($c->isMasterCollection());
if ($canAliasBlockOut) {
	$ct = PageType::getByID($c->getPageTypeID());
}

$isAlias = $b->isAlias();
$u = new User();
$numChildren = (!$isAlias) ? $b->getNumChildren() : 0;
if ($isAlias) {
	$deleteMessage = t('Do you want to delete this block?');
} else if ($numChildren) {
	$deleteMessage = t('Do you want to delete this block? This item is an original. If you delete it, you will delete all blocks aliased to it');
} else {
	$deleteMessage = t('Do you want to delete this block?');
}

?>

<div
    custom-style="<?=$b->getBlockCustomStyleRuleID()?>"
    data-area-id="<?=$a->getAreaID()?>"
    data-block-id="<?=$b->getBlockID()?>"
    class="<?=$class?>"
    data-block-type-handle="<?=$btHandle?>"
    data-launch-block-menu="block-menu-b<?=$b->getBlockID()?>-<?=$a->getAreaID()?>"
    data-dragging-avatar="<?=h('<p><img src="' . Loader::helper('concrete/urls')->getBlockTypeIconURL($btw) . '" /><span>' . t($btw->getBlockTypeName()) . '</span></p>')?>"
    <? if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) { ?> data-block-menu-handle="none"<? } ?>
>
    <ul class="ccm-edit-mode-inline-commands ccm-ui">
        <? if ($p->canEditBlock() && $btw->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY && (!$a->isGlobalArea())) {  ?>
            <li><a data-inline-command="move-block" href="#"><i class="fa fa-move"></i></a></li>
        <? } ?>
    </ul>

<div class="ccm-ui">

<div class="popover fade" data-block-menu="block-menu-b<?=$b->getBlockID()?>-<?=$a->getAreaID()?>">
	<div class="arrow"></div>
	<div class="popover-inner">
	<ul class="dropdown-menu">
	
	<? if ($btOriginal->getBlockTypeHandle() == BLOCK_HANDLE_STACK_PROXY) { 
		if (is_object($_bo)) {
			$bi = $_bo->getInstance();
		} else { 
			$bi = $b->getInstance();
		}
		$stack = Stack::getByID($bi->stID);
		if (is_object($stack)) {
			$sp = new Permissions($stack);
			if ($sp->canWrite()) { ?>

			<li><a href="<?=View::url('/dashboard/blocks/stacks', 'view_details', $stack->getCollectionID())?>"><?=t("Manage Stack Contents")?></a></li>		

			<? } 
		}
	} else if ($p->canEditBlock() && $b->isEditable()) { ?>

		<? if ($editInline) { ?>

			<? if ($b->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) { ?>
				<li><a href="javascript:void(0)" data-menu-action="edit_inline" data-area-grid-column-span="<?=$a->getAreaGridColumnSpan()?>"><?=t("Edit Layout")?></a></li>		
			<? } else { ?>
				<li><a href="javascript:void(0)" data-menu-action="edit_inline" data-area-grid-column-span="<?=$a->getAreaGridColumnSpan()?>"><?=t("Edit Block")?></a></li>		
				<? } ?>
		<? } else { ?>
			<li><a data-menu-action="block_dialog" data-menu-href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=edit" dialog-title="<?=t('Edit %s', $btOriginal->getBlockTypeName())?>" dialog-modal="false" dialog-width="<?=$btOriginal->getBlockTypeInterfaceWidth()?>" dialog-height="<?=$btOriginal->getBlockTypeInterfaceHeight() + $heightPlus?>" ><?=t("Edit Block")?></a></li>		
		<? } ?>

	<? } ?>

	<? if ($b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) { ?>
	<li><a href="javascript:void(0)" data-menu-action="block_scrapbook"><?=t("Copy to Clipboard")?></a></li>		
	<? } ?>


	<? if ($p->canDeleteBlock()) {  ?>
		<li><a href="javascript:void(0)" data-menu-action="delete_block" data-menu-delete-message="<?=$deleteMessage?>"><?=t("Delete")?></a></li>		
	<? } ?>

	<? if ($b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) { ?>

		<? if ($canDesign && $p->canEditBlockCustomTemplate()) { ?>
			<li class="divider"></li>

			<? if ($canDesign) { ?>
				<li><a dialog-title="<?=t('Custom Style')?>" dialog-modal="false" dialog-width="475" dialog-height="500" data-menu-action="block_dialog" data-menu-href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=block_css" ><?=t("Design")?></a></li>		
			<? } ?>

			<? if ($p->canEditBlockCustomTemplate()) { ?>
				<li><a dialog-title="<?=t('Custom Template')?>" dialog-modal="false" dialog-width="300" dialog-height="275" data-menu-action="block_dialog" data-menu-href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=template" ><?=t("Custom Template")?></a></li>		
			<? } ?>
		<? } ?>

		<? if ($canModifyGroups || $canScheduleGuestAccess || $canAliasBlockOut) { ?>
			<li class="divider"></li>
			<? if ($canModifyGroups) { ?>
				<li><a dialog-title="<?=t('Block Permissions')?>" dialog-modal="false" dialog-width="350" dialog-height="420" data-menu-action="block_dialog" data-menu-href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=groups" ><?=t("Permissions")?></a></li>		
			<? } ?>
			<? if ($canScheduleGuestAccess) { ?>
				<li><a dialog-title="<?=t('Schedule Guest Access')?>" dialog-modal="false" dialog-width="500" dialog-height="220" data-menu-action="block_dialog" data-menu-href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=guest_timed_access" ><?=t("Schedule Guest Access")?></a></li>		
			<? } ?>
			<? if ($canAliasBlockOut) { ?>
				<li><a dialog-title="<?=t('Setup on Child Pages')?>" dialog-modal="false" dialog-width="550" dialog-height="450" data-menu-action="block_dialog" data-menu-href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=child_pages" ><?=t("Setup on Child Pages")?></a></li>		
			<? } ?>
		<? } ?>
	<? } ?>

	</ul>
	</div>
</div>

</div>