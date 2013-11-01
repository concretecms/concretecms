<? 
	defined('C5_EXECUTE') or die("Access Denied.");
	if ($a->isGlobalArea()) {
		$c = Page::getCurrentPage();
		$cID = $c->getCollectionID();
	} else {
		$cID = $b->getBlockCollectionID();
		$c = $b->getBlockCollectionObject();
	}
	$btw = BlockType::getByID($b->getBlockTypeID());
	$btOriginal = $btw;
	$bID = $b->getBlockID();
	$heightPlus = 20;
	if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
		$_bi = $b->getInstance();
		$_bo = Block::getByID($_bi->getOriginalBlockID());
		$btOriginal = BlockType::getByHandle($_bo->getBlockTypeHandle());
		$heightPlus = 80;
	}
	$isAlias = $b->isAlias();
	$u = new User();
	$numChildren = (!$isAlias) ? $b->getNumChildren() : 0;
	if ($isAlias) {
		//$message = 'This item is an alias. Editing it will create a new instance of this block.';
		$deleteMessage = t('Do you want to delete this block?');
	} else if ($numChildren) {
		$editMessage =  t('This block is aliased by other blocks. If you edit this block, your changes will effect those other blocks. Are you sure you want to edit this block?');
		$deleteMessage = t('Do you want to delete this block? This item is an original. If you delete it, you will delete all blocks aliased to it');
	} else {
		$deleteMessage = t('Do you want to delete this block?');
	}
	if ($_GET['step']) {
		$step = "&step={$_GET['step']}";
	}
?>
	

<script type="text/javascript">
$(function() {
<? $id = $bID . $a->getAreaID(); ?>

var ccm_menuObj<?=$id?> = {};
ccm_menuObj<?=$id?>.type = "BLOCK";
ccm_menuObj<?=$id?>.arHandle = '<?=$a->getAreaHandle()?>';
ccm_menuObj<?=$id?>.aID = <?=$a->getAreaID()?>;
ccm_menuObj<?=$id?>.bID = <?=$bID?>;
ccm_menuObj<?=$id?>.cID = <?=$cID?>;
<? if ($p->canWrite() && $btOriginal->getBlockTypeHandle() != BLOCK_HANDLE_STACK_PROXY) { ?>
ccm_menuObj<?=$id?>.canWrite =true;
<? if ($b->isEditable()) { ?>
	ccm_menuObj<?=$id?>.hasEditDialog = true;
<? } else { ?>
	ccm_menuObj<?=$id?>.hasEditDialog = false;
<? } ?>
ccm_menuObj<?=$id?>.btName = <?=Loader::helper('json')->encode(t($btOriginal->getBlockTypeName()))?>;
ccm_menuObj<?=$id?>.width = <?=$btOriginal->getBlockTypeInterfaceWidth()?>;
ccm_menuObj<?=$id?>.height = <?=$btOriginal->getBlockTypeInterfaceHeight()+$heightPlus ?>;
<? } else if ($btOriginal->getBlockTypeHandle() == BLOCK_HANDLE_STACK_PROXY) { 
	if (is_object($_bo)) {
		$bi = $_bo->getInstance();
	} else { 
		$bi = $b->getInstance();
	}
	$stack = Stack::getByID($bi->stID);
	if (is_object($stack)) {
		$sp = new Permissions($stack);
		if ($sp->canWrite()) {
		?>
		ccm_menuObj<?=$id?>.canWriteStack =true;
		ccm_menuObj<?=$id?>.stID = <?=$bi->stID?>;
		<? } 
	}
}
?>
ccm_menuObj<?=$id?>.canCopyToScrapbook = true;
<? if ($p->canEditBlockPermissions() && PERMISSIONS_MODEL != 'simple' && (!$a->isGlobalArea())) { ?>
ccm_menuObj<?=$id?>.canModifyGroups = true;
<? }
if (PERMISSIONS_MODEL != 'simple' && $p->canGuestsViewThisBlock() && $p->canScheduleGuestAccess() && (!$a->isGlobalArea())) { ?>
	ccm_menuObj<?=$id?>.canScheduleGuestAccess = true;
<? }
if ($p->canEditBlockDesign() && ENABLE_CUSTOM_DESIGN == true) { ?>
	ccm_menuObj<?=$id?>.canDesign = true;
<? } else { ?>
	ccm_menuObj<?=$id?>.canDesign = false;
<? }
if ($p->canEditBlockCustomTemplate()) { ?>
	ccm_menuObj<?=$id?>.canEditBlockCustomTemplate = true;
<? } else { ?>
	ccm_menuObj<?=$id?>.canEditBlockCustomTemplate = false;
<? }
if ($p->canEditBlockPermissions()) { ?>
ccm_menuObj<?=$id?>.canAdmin = true;
<? }
if ($p->canDeleteBlock()) { ?>
ccm_menuObj<?=$id?>.canDelete = true;
ccm_menuObj<?=$id?>.deleteMessage = "<?=$deleteMessage?>";
<? }
if ($c->isMasterCollection() && !$a->isGlobalArea()) { ?>
ccm_menuObj<?=$id?>.canAliasBlockOut = true;
<?
$ct = CollectionType::getByID($c->getCollectionTypeID());
if ($ct->isCollectionTypeIncludedInComposer()) { ?>
	ccm_menuObj<?=$id?>.canSetupComposer = true;
<? }

}

if ($p->canWrite() && (!$a->isGlobalArea())) {  ?>
	ccm_menuObj<?=$id?>.canArrange = true;
<? 
}
if ($editMessage) { ?>
ccm_menuObj<?=$id?>.editMessage = "<?=$editMessage?>";
<? } ?>
ccm_menuInit(ccm_menuObj<?=$id?>);
});

</script>
