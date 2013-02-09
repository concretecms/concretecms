<?
defined('C5_EXECUTE') or die("Access Denied.");

if (!Loader::helper('validation/numbers')->integer($_REQUEST['cID'])) {
	die(t('Access Denied'));
}

$c = Page::getByID($_REQUEST['cID']); 
$cp = new Permissions($c);
if (!$cp->canEditPageContents()) {
	die(t('Access Denied'));
}

$btl = new BlockTypeList();
$blockTypes = $btl->getBlockTypeList();
$dsh = Loader::helper('concrete/dashboard');
$dashboardBlockTypes = array();
if ($dsh->inDashboard()) {
	$dashboardBlockTypes = BlockTypeList::getDashboardBlockTypes();
}
$blockTypes = array_merge($blockTypes, $dashboardBlockTypes);

$ih = Loader::helper('concrete/interface');
$ci = Loader::helper('concrete/urls');
?>


<script type="text/javascript">

$(function() {
	ccm_activateBlockTypeOverlay();
});
</script>

<div class="ccm-ui" id="ccm-block-types-wrapper">

<form class="form-inline" id="ccm-block-type-search">
	<i class="icon-search"></i> <input type="search" />
</form>

<?
$tabs = array();
$sets = BlockTypeSet::getList();
for ($i = 0; $i < count($sets); $i++) {
	$bts = $sets[$i];
	$tabs[] = array($bts->getBlockTypeSetHandle(), $bts->getBlockTypeSetName(), $i == 0);
}
if ($dsh->inDashboard()) {
	$tabs[] = array('dashboard', t('Dashboard'));
}

print $ih->tabs($tabs, true, 'ccm_activateBlockTypeTabs');
?>

<ul id="ccm-overlay-block-types">

<? foreach($blockTypes as $bt) { 
	if (!$cp->canAddBlockType($bt)) {
		continue;
	}
	$btsets = $bt->getBlockTypeSets();
	$sets = '';
	foreach($btsets as $set) {
		$sets .= $set->getBlockTypeSetHandle() . ' ';
	}
	$sets = trim($sets);
	$btIcon = $ci->getBlockTypeIconURL($bt);

	?>

	<li data-block-type-sets="<?=$sets?>">
		<a class="ccm-overlay-draggable-block-type" data-block-type-handle="<?=$bt->getBlockTypeHandle()?>" data-dialog-title="<?=t('Add %s', $bt->getBlockTypeName())?>" data-dialog-width="<?=$bt->getBlockTypeInterfaceWidth()?>" data-dialog-height="<?=$bt->getBlockTypeInterfaceHeight()?>" data-has-add-template="<?=$bt->hasAddTemplate()?>" data-supports-inline-editing="<?=$bt->supportsInlineEditing()?>" data-btID="<?=$bt->getBlockTypeID()?>" href="javascript:void(0)"><p><img src="<?=$btIcon?>" /><span><?=$bt->getBlockTypeName()?></span></p></a>
	</li>
	
<? } ?>

</ul>

</div>