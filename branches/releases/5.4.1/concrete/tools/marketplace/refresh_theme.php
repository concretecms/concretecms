<?php   defined('C5_EXECUTE') or die("Access Denied.");

$ch = Loader::helper('concrete/interface');

//marketplace
if (ENABLE_MARKETPLACE_SUPPORT) {
	Loader::model('marketplace_remote_item');
	$mri = new MarketplaceRemoteItemList();
	$mri->filterByIsFeaturedRemotely(1);
	$mri->setType('themes');
	$mri->execute();
	$availableThemes = $mri->getPage();
} else {
	$availableThemes=array();
}
?>

<script type="text/javascript">
ccm_marketplaceRefreshInstalledThemes = function() {
	jQuery.fn.dialog.closeTop();
	$("a#ccm-nav-design").click();
}
</script>

<h2><?php echo t('Themes')?></h2> 

	<?php  if( !count($availableThemes) ){ ?>
		<div><?php echo t('Unable to connect to the marketplace.')?></div>
	<?php  }else{ ?>
		<div class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?php echo ceil(count($availableThemes)/4)?>" >
			<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
			<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>
						
			<div class="ccm-scroller-inner">
				<ul id="ccm-select-marketplace-theme" style="width: <?php echo count($availableThemes) * 132?>px">			
				<?php  foreach($availableThemes as $availableTheme){ ?>
					<li class="themeWrap">
						<span class="ccm-button-marketplace-install"><a href="javascript:void(0)" onclick="ccm_getMarketplaceItem({mpID: '<?php echo $availableTheme->getMarketplaceItemID()?>', onComplete: function() {ccm_marketplaceRefreshInstalledThemes()}})" title="<?php echo t('Install theme')?>"><img src="<?php echo $availableTheme->getRemoteListIconURL() ?>" width="97" height="97" /></a></span>
							<a title="<?php echo t('Preview')?>" onclick="ccm_previewMarketplaceTheme(<?php echo $_REQUEST['cID']?>, <?php echo intval($availableTheme->getRemoteCollectionID())?>,'<?php echo addslashes($availableTheme->getName()) ?>','<?php echo addslashes($availableTheme->getHandle()) ?>')" href="javascript:void(0)" class="preview">
							<img src="<?php echo ASSETS_URL_IMAGES?>/icons/magnifying.png" alt="<?php echo t('Preview')?>" class="ccm-preview" /></a>
						<div class="ccm-theme-name" ><a target="_blank" href="<?php echo $availableTheme->getRemoteURL() ?>"><?php echo $availableTheme->getName() ?></a></div>
					</li>
				<?php  } ?> 
				</ul>
			</div>
		</div>			
	<?php  } ?> 	
