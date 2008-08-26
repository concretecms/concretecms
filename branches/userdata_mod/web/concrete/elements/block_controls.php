<? 
	$cID = $b->getBlockCollectionID();
	$c = $b->getBlockCollectionObject();
	$btw = BlockType::getByID($b->getBlockTypeID());
	$bID = $b->getBlockID();
	$arHandle = $a->getAreaHandle();
	$isAlias = $b->isAlias();
	$u = new User();
	$numChildren = (!$isAlias) ? $b->getNumChildren() : 0;
	if ($isAlias) {
		//$message = 'This item is an alias. Editing it will create a new instance of this block.';
		$deleteMessage = 'Do you want to delete this block?';
	} else if ($numChildren) {
		$editMessage = 'This block is aliased by other blocks.\nIf you edit this block, your changes will effect those other blocks.\n\nAre you sure you want to edit this block?';
		$deleteMessage = 'Do you want to delete this block? This item is an original. If you delete it, you will delete all blocks aliased to it';
	} else {
		$deleteMessage = 'Do you want to delete this block?';
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
<? if ($b->isEditable() && $p->canWrite()) { ?>
ccm_menuObj<?=$id?>.canWrite =true;
ccm_menuObj<?=$id?>.width = <?=$btw->getBlockTypeInterfaceWidth()?>;
ccm_menuObj<?=$id?>.height = <?=$btw->getBlockTypeInterfaceHeight()?>;
<? }
if ($p->canAdminBlock() && PERMISSIONS_MODEL != 'simple') { ?>
ccm_menuObj<?=$id?>.canModifyGroups = true;
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
<? } 
if ($p->canWrite()) {  ?>
	ccm_menuObj<?=$id?>.canArrange = true;
<? 
}
if ($editMessage) { ?>
ccm_menuObj<?=$id?>.editMessage = "<?=$editMessage?>";
<? } ?>
$(function() {ccm_menuInit(ccm_menuObj<?=$id?>)});

</script>