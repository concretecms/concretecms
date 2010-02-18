<?  defined('C5_EXECUTE') or die(_("Access Denied."));

$ch = Loader::helper('concrete/interface');

//marketplace
if(ENABLE_MARKETPLACE_SUPPORT){
	$marketplaceBlocksHelper = Loader::helper('concrete/marketplace/blocks'); 
	$marketplaceBlockTypes = $marketplaceBlocksHelper->getPreviewableList();
}else{
	$marketplaceBlockTypes=array();
}

?>

<script type="text/javascript">
ccm_marketplaceRefreshInstalledBlockTypes = function() {
	jQuery.fn.dialog.closeTop();
	setTimeout(function() {
	<? if ($_REQUEST['arHandle']) { ?>
		ccm_openAreaAddBlock('<?=$_REQUEST['arHandle']?>');
	<? } ?>
	}, 500);
	jQuery.fn.dialog.closeTop();
}
</script>

<? if (count($marketplaceBlockTypes) > 0) { ?>

	<table class="ccm-block-type-table">

	<? foreach($marketplaceBlockTypes as $bt) { 
		$btIcon = $bt->getRemoteListIconURL();

		$btButton = t("Download");
		$btClass = "";
		$btDesc = $bt->getDescription();
		$btButton = t("Get");


		?>	
		<tr class="ccm-block-type-row">
			<td<?=!empty($btDesc)?' valign="top"':''?>><img src="<?=$btIcon?>" /></td>
			<td width="90%">
				<div class="ccm-block-type-inner"><?=$bt->getName()?></div>
			<? if (!empty($btDesc)) { ?>
				<div class="ccm-block-type-description" id="ccm-bt-help<?=$bt->getHandle()?>"><?=$btDesc?></div>
			<? } ?>
			</td>
			<td><div class="ccm-block-price"><? if ($bt->getPrice() == '0.00') { print t('Free'); } else { print '$' . $bt->getPrice(); } ?></div></td>
			<td<?=$btClass?>><?=$ch->button_js($btButton, 'ccm_getMarketplaceItem({mpID: \'' . $bt->getMarketplaceItemID() . '\', onComplete: function() {ccm_marketplaceRefreshInstalledBlockTypes()}})', "right", NULL);?></td>
		</tr>
	<? } ?>
		</table>
<? } else { ?>
		<p><?=t('Unable to connect to the marketplace.')?></p>
<? } ?>