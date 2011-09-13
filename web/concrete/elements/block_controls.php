<? 
	defined('C5_EXECUTE') or die("Access Denied.");
	$cID = $b->getBlockCollectionID();
	$c = $b->getBlockCollectionObject();
	$btw = BlockType::getByID($b->getBlockTypeID());
	$bID = $b->getBlockID();
	if ($rarHandle) { 
		$arHandle = $rarHandle;
	} else {
		$arHandle = $a->getAreaHandle();
	}
	$isAlias = $b->isAlias();
	$isGlobal = $b->isGlobal();
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
<? $id = $bID . $a->getAreaID(); ?>

ccm_menuObj<?=$id?> = new Object();
ccm_menuObj<?=$id?>.type = "BLOCK";
ccm_menuObj<?=$id?>.arHandle = '<?=$arHandle?>';
ccm_menuObj<?=$id?>.aID = <?=$a->getAreaID()?>;
ccm_menuObj<?=$id?>.bID = <?=$bID?>;
ccm_menuObj<?=$id?>.isGlobal = <?=intval($isGlobal)?>;
<? if ($b->isEditable() && $p->canWrite()) { ?>
ccm_menuObj<?=$id?>.canWrite =true;
ccm_menuObj<?=$id?>.btName = "<?=$btw->getBlockTypeName()?>";
ccm_menuObj<?=$id?>.width = <?=$btw->getBlockTypeInterfaceWidth()?>;
ccm_menuObj<?=$id?>.height = <?=(!$isGlobal)?$btw->getBlockTypeInterfaceHeight():$btw->getBlockTypeInterfaceHeight()+20 ?>;
<? }
if ($p->canAdminBlock() && PERMISSIONS_MODEL != 'simple') { ?>
ccm_menuObj<?=$id?>.canModifyGroups = true;
<? }
if ($p->canWrite() && ENABLE_CUSTOM_DESIGN == true) { ?>
	ccm_menuObj<?=$id?>.canDesign = true;
<? } else { ?>
	ccm_menuObj<?=$id?>.canDesign = false;
<? }
if ($p->canAdminBlock()) { ?>
ccm_menuObj<?=$id?>.canAdmin = true;
<? }
if ($p->canDeleteBlock()) { ?>
ccm_menuObj<?=$id?>.canDelete = true;
ccm_menuObj<?=$id?>.deleteMessage = "<?=$deleteMessage?>";
<? }
if ($c->isMasterCollection()) { ?>
ccm_menuObj<?=$id?>.canAliasBlockOut = true;
<?
$ct = CollectionType::getByID($c->getCollectionTypeID());
if ($ct->isCollectionTypeIncludedInComposer()) { ?>
	ccm_menuObj<?=$id?>.canSetupComposer = true;
<? }

}

if ($p->canWrite()) {  ?>
	ccm_menuObj<?=$id?>.canArrange = true;
<? 
}
if ($editMessage) { ?>
ccm_menuObj<?=$id?>.editMessage = "<?=$editMessage?>";
<? } ?>
head.ready(function() {ccm_menuInit(ccm_menuObj<?=$id?>)});

</script>