<?  defined('C5_EXECUTE') or die("Access Denied.");?>
<div class="ccm-ui">
<?

Loader::library('marketplace');
$mi = Marketplace::getInstance();
$tp = new TaskPermission();
if (!$tp->canInstallPackages()) { ?>
	<p><?=t('You do not have permission to download packages from the marketplace.')?></p>
	<? exit;
} else if (!$mi->isConnected()) { ?>
	<div class="ccm-pane-body-inner">
		<? Loader::element('dashboard/marketplace_connect_failed')?>
	</div>
<? } else {	
	$ch = Loader::helper('concrete/interface'); 
	Loader::library('marketplace');
	Loader::model('marketplace_remote_item');
	
	$mpID = $_REQUEST['mpID'];
	if (!empty($mpID)) {
		$mri = MarketplaceRemoteItem::getByID($mpID);
	}
	if (is_object($mri)) { ?>
	
		<? $screenshots = $mri->getScreenshots(); ?>
		
		
		<table class="ccm-marketplace-details-table">
		<tr>
			<td valign="top">
				<div id="ccm-marketplace-item-screenshots-wrapper">	
				<div id="ccm-marketplace-item-screenshots">
				<?
				$i = 0;
				
				if (count($screenshots) > 0) { 
					foreach($screenshots as $si) { ?>
						<img src="<?=$si->src?>" width="<?=$si->width?>" height="<?=$si->height?>" <? if ($i != 0) { ?>style="display: none" <? } ?> />	
					<? 
					$i++;
					}
					
				} else { ?>
					<div class="ccm-marketplace-item-screenshots-none">
						<?=t('No screenshots')?>
					</div>
				<? } ?>
				</div>
				</div>
						
				<? if (!$mri->getMarketplaceItemVersionForThisSite()) { ?>
					<Div class="clearfix" style="clear: both">
					<div class="block-message alert-message error">
						<p><?=t('This add-on is marked as incompatible with this version of concrete5. Please contact the author of the add-on for assistance.')?></p>
					</div>
					</div>
				<? } ?>
			</td>
			<td valign="top">
			
		<div class="ccm-marketplace-item-information">
		<div class="ccm-marketplace-item-information-inner">
		<h1><?=$mri->getName()?></h1>
		</div>

		<? if ($mri->getReviewBody() != '') { ?>
			<div class="ccm-marketplace-item-review-quote">
			<?=$mri->getReviewBody()?>
			</div>
		<? } ?>
		<div class="ccm-marketplace-item-rating">
			<?=Loader::helper('rating')->outputDisplay($mri->getAverageRating())?>
			<?=$mri->getTotalRatings()?> <?= ($mri->getTotalRatings() == 1) ? t('review') : t('reviews'); ?>
			<? if ($mri->getTotalRatings() > 0) { ?>
				<a href="<?=$mri->getRemoteReviewsURL()?>" target="_blank" class="ccm-marketplace-item-reviews-link"><?=t('Read Reviews')?></a>
			<? } ?>
		</div>

		<div>
		<h2><?=t('Details')?></h2>
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
		
		if (!$mri->getMarketplaceItemVersionForThisSite()) {
			$buttonAction = 'javascript:void(0)';
		}

	?>
	
		<div class="dialog-buttons">
			<input type="button" class="btn primary <? if (!$mri->getMarketplaceItemVersionForThisSite()) { ?> disabled<? } ?> ccm-button-right" value="<?=$buttonText?>" onclick="<?=$buttonAction?>" />
			<input type="button" class="btn" value="<?=t('View in Marketplace')?>" onclick="window.open('<?=$mri->getRemoteURL()?>')" /> 
			<? if ($mri->getMarketplaceItemType() == 'theme') { ?>
				<a title="<?php echo t('Preview')?>" onclick="ccm_previewMarketplaceTheme(1, <?php echo intval($mri->getRemoteCollectionID())?>,'<?php echo addslashes($mri->getName()) ?>','<?php echo addslashes($mri->getHandle()) ?>')" 
				href="javascript:void(0)" class="btn"><?=t('Preview')?></a>
			<? } ?>
		</div>
		<br/>
		
	
	</td>
	</tr>
	</table>
	
	<? } else { ?>
		<div class="block-message alert-message error"><p><?=t('Invalid marketplace item.')?></p></div>
	<? } ?>

<? } ?>

</div>
<script type="text/javascript">
var marketplaceImages;
$(function() {
	clearInterval(marketplaceImages);
	var currentImage = 0;
	var totalImages = $('#ccm-marketplace-item-screenshots img').length;
	if (totalImages > 1) {
		var im = $('#ccm-marketplace-item-screenshots img').eq(0);
		im.css('z-index', 10001);
		$('#ccm-marketplace-item-screenshots img').not(im).css('z-index', 10000);
		marketplaceImages = setInterval(function() {
			var oim = $('#ccm-marketplace-item-screenshots img').eq(currentImage);
			currentImage++;
			if (currentImage == totalImages) {
				currentImage = 0;
			}
			var nim = $('#ccm-marketplace-item-screenshots img').eq(currentImage);
			nim.show();
			oim.fadeOut(500, function() {
				oim.css('z-index', 10000);
				nim.css('z-index', 10001);
			});
		}, 5000);
	}	
});
</script>