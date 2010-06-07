<?  defined('C5_EXECUTE') or die(_("Access Denied."));

$ch = Loader::helper('concrete/interface');

//marketplace
if(ENABLE_MARKETPLACE_SUPPORT){
	Loader::model('marketplace_remote_item');
	$mri = new MarketplaceRemoteItemList();
	$mri->filterByIsFeaturedRemotely(1);
	$mri->setIncludeInstalledItems(true);
	$mri->setType('addons');
	$mri->execute();
	$marketplaceBlockTypes = $mri->getPage();
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
		if ($bt->purchaseRequired()) { 
			$btButton = t("Purchase");
		} else {
			$btButton = t("Download");
		}
		$btClass = "";
		$btDesc = $bt->getDescription();


		?>	
		<tr class="ccm-block-type-row">
			<td<?=!empty($btDesc)?' valign="top"':''?>><img src="<?=$btIcon?>" /></td>
			<td><div style="width: 7px">&nbsp;</td>
			<td width="90%">
				<div class="ccm-block-type-inner" style="border: 0px"><?=$bt->getName()?></div>
			<? if (!empty($btDesc)) { ?>
				<div class="ccm-block-type-description" id="ccm-bt-help<?=$bt->getHandle()?>"><?=$btDesc?></div>
			<? } ?>
			</td>
			<td><div class="ccm-block-price"><? if ($bt->getPrice() == '0.00') { print t('Free'); } else { print '$' . $bt->getPrice(); } ?></div></td>
			<? if (!$bt->purchaseRequired()) { ?>
				<td<?=$btClass?>><?=$ch->button_js($btButton, 'ccm_getMarketplaceItem({mpID: \'' . $bt->getMarketplaceItemID() . '\', onComplete: function() {ccm_marketplaceRefreshInstalledBlockTypes()}})', "right", NULL);?></td>
			<? } else { ?>
				<td<?=$btClass?>><?=$ch->button_js($btButton, 'window.open(\'' . $bt->getRemoteURL() . '\')', "right", NULL);?></td>
			<? } ?>
		</tr>
	<? } ?>
		</table>
<? } else { ?>
		<p><?=t('Unable to connect to the marketplace.')?></p>
<? } ?>