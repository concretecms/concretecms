<?php   defined('C5_EXECUTE') or die("Access Denied.");

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
	<?php  if ($_REQUEST['arHandle']) { ?>
		ccm_openAreaAddBlock('<?php echo $_REQUEST['arHandle']?>');
	<?php  } ?>
	}, 500);
	jQuery.fn.dialog.closeTop();
}
</script>

<?php  if (count($marketplaceBlockTypes) > 0) { ?>

	<table class="ccm-block-type-table">

	<?php  foreach($marketplaceBlockTypes as $bt) { 
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
			<td<?php echo !empty($btDesc)?' valign="top"':''?>><img src="<?php echo $btIcon?>" /></td>
			<td><div style="width: 7px">&nbsp;</td>
			<td width="90%">
				<div class="ccm-block-type-inner" style="border: 0px"><?php echo $bt->getName()?></div>
			<?php  if (!empty($btDesc)) { ?>
				<div class="ccm-block-type-description" id="ccm-bt-help<?php echo $bt->getHandle()?>"><?php echo $btDesc?></div>
			<?php  } ?>
			</td>
			<td><div class="ccm-block-price"><?php  if ($bt->getPrice() == '0.00') { print t('Free'); } else { print '$' . $bt->getPrice(); } ?></div></td>
			<?php  if (!$bt->purchaseRequired()) { ?>
				<td<?php echo $btClass?>><?php echo $ch->button_js($btButton, 'ccm_getMarketplaceItem({mpID: \'' . $bt->getMarketplaceItemID() . '\', onComplete: function() {ccm_marketplaceRefreshInstalledBlockTypes()}})', "right", NULL);?></td>
			<?php  } else { ?>
				<td<?php echo $btClass?>><?php echo $ch->button_js($btButton, 'window.open(\'' . $bt->getRemoteURL() . '\')', "right", NULL);?></td>
			<?php  } ?>
		</tr>
	<?php  } ?>
		</table>
<?php  } else { ?>
		<p><?php echo t('Unable to connect to the marketplace.')?></p>
<?php  } ?>