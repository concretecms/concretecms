<? 
defined('C5_EXECUTE') or die("Access Denied.");
$c = $b->getBlockCollectionObject();
if (!is_object($a)) {
	$a = Area::get($c, $b->getAreaHandle());
}

$arHandle = $a->getAreaHandle();

$btw = BlockType::getByID($b->getBlockTypeID());
if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
	$class = 'ccm-block-edit-layout ccm-block-arrangeable';
} else {
	$class = 'ccm-block-edit ccm-block-arrangeable';
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
if ($btw->supportsInlineEditing()) {
	$editInline = true;
}
$btOriginal = $btw;
$bID = $b->getBlockID();
$aID = $a->getAreaID();
$heightPlus = 20;
if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
	$_bi = $b->getInstance();
	$_bo = Block::getByID($_bi->getOriginalBlockID());
	$btOriginal = BlockType::getByHandle($_bo->getBlockTypeHandle());
	$heightPlus = 80;
	if ($btOriginal->supportsInlineEditing()) {
		$editInline = true;
	}
}
$canDesign = ($p->canEditBlockDesign() && ENABLE_CUSTOM_DESIGN == true);
$canModifyGroups = ($p->canEditBlockPermissions() && PERMISSIONS_MODEL != 'simple' && (!$a->isGlobalArea()));
$canScheduleGuestAccess = (PERMISSIONS_MODEL != 'simple' && $p->canGuestsViewThisBlock() && $p->canScheduleGuestAccess() && (!$a->isGlobalArea()));
$canAliasBlockOut = ($c->isMasterCollection());
if ($canAliasBlockOut) {
	$ct = CollectionType::getByID($c->getCollectionTypeID());
	$canSetupComposer = ($ct->isCollectionTypeIncludedInComposer());
}

$isAlias = $b->isAlias();
$u = new User();
$numChildren = (!$isAlias) ? $b->getNumChildren() : 0;
if ($b->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
	$deleteMessage = t('Do you want to delete this layout? This will remove all blocks inside it.');
} else if ($isAlias) {
	$deleteMessage = t('Do you want to delete this block?');
} else if ($numChildren) {
	$deleteMessage = t('Do you want to delete this block? This item is an original. If you delete it, you will delete all blocks aliased to it');
} else {
	$deleteMessage = t('Do you want to delete this block?');
}

?>

<div id="b<?=$b->getBlockID()?>-<?=$a->getAreaID()?>" custom-style="<?=$b->getBlockCustomStyleRuleID()?>" class="<?=$class?>" <? if ($btw->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) { ?>data-handle="block-menu-b<?=$b->getBlockID()?>-<?=$a->getAreaID()?>" <? } ?>>

<div class="ccm-ui">

<div class="popover fade"  data-menu="block-menu-b<?=$b->getBlockID()?>-<?=$a->getAreaID()?>">
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

			<li><a href="<?=$this->url('/dashboard/blocks/stacks', 'view_details', $stack->getCollectionID())?>"><i class="icon-list"></i> <?=t("Manage Stack Contents")?></a></li>		

			<? } 
		}
	} else if ($p->canEditBlock() && $b->isEditable()) { ?>

		<? if ($editInline) { ?>
			<? $params = 'false'; ?>

			<? if ($a->getAreaGridColumnSpan() > 0) {
				$params = '{arGridColumnSpan: ' . $a->getAreaGridColumnSpan() . '}';
			} ?>

			<? if ($b->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) { ?>
				<li><a href="javascript:void(0)" onclick="ccm_loadInlineEditor('<?=$cID?>','<?=htmlspecialchars($arHandle)?>','<?=$aID?>','<?=$bID?>', <?=$params?>)"><i class="icon-pencil"></i> <?=t("Edit Layout")?></a></li>		
			<? } else { ?>
				<li><a href="javascript:void(0)" onclick="ccm_loadInlineEditor('<?=$cID?>','<?=htmlspecialchars($arHandle)?>','<?=$aID?>','<?=$bID?>', <?=$params?>)"><i class="icon-pencil"></i> <?=t("Edit Block")?></a></li>		
				<? } ?>
		<? } else { ?>
			<li><a class="dialog-launch" dialog-title="<?=t('Edit %s', $btOriginal->getBlockTypeName())?>" dialog-modal="false" dialog-on-close="ccm_blockWindowAfterClose()" dialog-width="<?=$btOriginal->getBlockTypeInterfaceWidth()?>" dialog-height="<?=$btOriginal->getBlockTypeInterfaceHeight() + $heightPlus?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?cID=<?=$cID?>&amp;bID=<?=$bID?>&amp;arHandle=<?=htmlspecialchars($arHandle)?>&amp;btask=edit" ><i class="icon-pencil"></i> <?=t("Edit Block")?></a></li>		
		<? } ?>

	<? } ?>

	<? if ($b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) { ?>
	<li><a href="javascript:void(0)" onclick="ccm_addToScrapbook('<?=$cID?>','<?=$bID?>', '<?=htmlspecialchars($arHandle)?>')"><i class="icon-heart"></i> <?=t("Copy to Clipboard")?></a></li>		
	<? } ?>

	<? if ($p->canEditBlock() && (!$a->isGlobalArea())) {  ?>
		<li><a href="javascript:void(0)" onclick="ccm_arrangeInit()"><i class="icon-move"></i> <?=t("Move")?></a></li>		
	<? } ?>

	<? if ($p->canDeleteBlock()) {  ?>
		<li><a href="javascript:void(0)" onclick="ccm_deleteBlock('<?=$cID?>','<?=$bID?>','<?=$aID?>','<?=htmlspecialchars($arHandle)?>', '<?=$deleteMessage?>')"><i class="icon-trash"></i> <?=t("Delete")?></a></li>		
	<? } ?>

	<? if ($b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) { ?>

		<? if ($canDesign && $p->canEditBlockCustomTemplate()) { ?>
			<li class="divider"></li>

			<? if ($canDesign) { ?>
				<li><a class="dialog-launch" dialog-title="<?=t('Custom Style')?>" dialog-modal="false" dialog-width="475" dialog-height="500" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?cID=<?=$cID?>&amp;bID=<?=$bID?>&amp;arHandle=<?=htmlspecialchars($arHandle)?>&amp;btask=block_css&amp;modal=true" ><i class="icon-font"></i> <?=t("Design")?></a></li>		
			<? } ?>

			<? if ($p->canEditBlockCustomTemplate()) { ?>
				<li><a class="dialog-launch" dialog-title="<?=t('Custom Template')?>" dialog-modal="false" dialog-width="300" dialog-height="275" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?cID=<?=$cID?>&amp;bID=<?=$bID?>&amp;arHandle=<?=htmlspecialchars($arHandle)?>&amp;btask=template&amp;modal=true" ><i class="icon-file"></i> <?=t("Custom Template")?></a></li>		
			<? } ?>
		<? } ?>

		<? if ($canModifyGroups || $canScheduleGuestAccess || $canAliasBlockOut || $canSetupComposer) { ?>
			<li class="divider"></li>
			<? if ($canModifyGroups) { ?>
				<li><a class="dialog-launch" dialog-title="<?=t('Block Permissions')?>" dialog-modal="false" dialog-width="350" dialog-height="420" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?cID=<?=$cID?>&amp;bID=<?=$bID?>&amp;arHandle=<?=htmlspecialchars($arHandle)?>&amp;btask=groups" ><i class="icon-lock"></i> <?=t("Permissions")?></a></li>		
			<? } ?>
			<? if ($canScheduleGuestAccess) { ?>
				<li><a class="dialog-launch" dialog-title="<?=t('Schedule Guest Access')?>" dialog-modal="false" dialog-width="500" dialog-height="220" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?cID=<?=$cID?>&amp;bID=<?=$bID?>&amp;arHandle=<?=htmlspecialchars($arHandle)?>&amp;btask=guest_timed_access" ><i class="icon-time"></i> <?=t("Schedule Guest Access")?></a></li>		
			<? } ?>
			<? if ($canAliasBlockOut) { ?>
				<li><a class="dialog-launch" dialog-title="<?=t('Setup on Child Pages')?>" dialog-modal="false" dialog-width="550" dialog-height="450" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?cID=<?=$cID?>&amp;bID=<?=$bID?>&amp;arHandle=<?=htmlspecialchars($arHandle)?>&amp;btask=child_pages" ><i class="icon-arrow-right"></i> <?=t("Setup on Child Pages")?></a></li>		
			<? } ?>
			<? if ($canSetupComposer) { ?>
				<li><a class="dialog-launch" dialog-title="<?=t('Composer Settings')?>" dialog-modal="false" dialog-width="500" dialog-height="220" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?cID=<?=$cID?>&amp;bID=<?=$bID?>&amp;arHandle=<?=htmlspecialchars($arHandle)?>&amp;btask=composer" ><i class="icon-cog"></i> <?=t("Composer Settings")?></a></li>		
			<? } ?>

		<? } ?>
	<? } ?>


	</ul>
	</div>
</div>

</div>