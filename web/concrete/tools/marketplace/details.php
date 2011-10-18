<?  defined('C5_EXECUTE') or die("Access Denied.");

$tp = new TaskPermission();
if (!$tp->canInstallPackages()) { ?>
	<p><?=t('You do not have permission to download packages from the marketplace.')?></p>
	<? exit;
}
$ch = Loader::helper('concrete/interface'); 
Loader::library('marketplace');
Loader::model('marketplace_remote_item');

$mpID = $_REQUEST['mpID'];
if (!empty($mpID)) {
	$mri = MarketplaceRemoteItem::getByID($mpID);
}
if (is_object($mri)) { ?>

	<? $screenshots = $mri->getScreenshots(); ?>
	
	
	<div id="ccm-marketplace-item-screenshots-outer">
	<div class="ccm-nivo-theme-default" id="ccm-marketplace-item-screenshots-wrapper">	
    <div class="ribbon"></div>
    <div id="ccm-marketplace-item-screenshots" class="nivoSlider">
	<?
	if (count($screenshots) > 0) { 
		foreach($screenshots as $si) { ?>
			<img src="<?=$si->src?>" width="<?=$si->width?>" height="<?=$si->height?>" />	
		<? }
	} else { ?>
		<img src="<?=ASSETS_URL_IMAGES?>/spacer.gif" width="400" height="400" />
	<? } ?>
	</div>
	</div>
	</div>
		
	<div class="ccm-marketplace-item-information">
	<div class="ccm-marketplace-item-information-inner">
	<h1><?=$mri->getName()?></h1>
	<p><?=$mri->getBody()?></p>	
	</div>
<?
	if ($mri->purchaseRequired()) {
		$buttonText = t('Purchase - %s', '$' . $mri->getPrice());
		$buttonAction = 'javascript:ccm_getMarketplaceItem({mpID: \'' . $mri->getMarketplaceItemID() . '\'})';
	} else {
		$buttonText = t('Download & Install');
		if ($type == 'themes') {
			$buttonAction = 'javascript:ccm_getMarketplaceItem({mpID: \'' . $mri->getMarketplaceItemID() . '\', onComplete: function() {window.location.href=\'' . View::url('/dashboard/pages/themes') . '\'}})';
		} else {
			$buttonAction = 'javascript:ccm_getMarketplaceItem({mpID: \'' . $mri->getMarketplaceItemID() . '\', onComplete: function() {window.location.href=\'' . View::url('/dashboard/extend/install') . '\'}})';					
		}
	}
?>
	<div class="ccm-marketplace-item-information-more">
		<a href="javascript:void(0)" onclick="ccm_marketplaceDetailShowMore()"><?=t('More Details')?></a>
	</div>
	
	<div class="ccm-marketplace-item-buttons">
		<input type="button" class="btn primary" value="<?=$buttonText?>" onclick="<?=$buttonAction?>" />&nbsp;&nbsp;<input type="button" class="btn" value="<?=t('View in Marketplace')?>" onclick="window.open('<?=$mri->getRemoteURL()?>')" />
	</div>
	</div>
		
<? } else { ?>
	<div class="block-message alert-message error"><p><?=t('Invalid marketplace item.')?></p></div>
<? } ?>