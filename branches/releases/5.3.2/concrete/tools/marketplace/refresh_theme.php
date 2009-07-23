<?php   defined('C5_EXECUTE') or die(_("Access Denied."));

$ch = Loader::helper('concrete/interface');

//marketplace
if (ENABLE_MARKETPLACE_SUPPORT) {
	$themesHelper = Loader::helper('concrete/marketplace/themes'); 
	$availableThemes=$themesHelper->getPreviewableList();
} else {
	$availableThemes=array();
}
?>

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
						<span class="ccm-button-marketplace-install"><a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED.'/package_install?type=theme&install=1&cID='.$availableTheme->getRemoteCollectionID()?>" title="<?php echo t('Install theme')?>"><img src="<?php echo $availableTheme->getThemeThumbnail() ?>" /></a></span>
							<a title="<?php echo t('Preview')?>" onclick="ccm_previewMarketplaceTheme(<?php echo $_REQUEST['cID']?>, <?php echo intval($availableTheme->getRemoteCollectionID())?>,'<?php echo addslashes($availableTheme->getThemeName()) ?>','<?php echo addslashes($availableTheme->getThemeHandle()) ?>')" href="javascript:void(0)" class="preview">
							<img src="<?php echo ASSETS_URL_IMAGES?>/icons/magnifying.png" alt="<?php echo t('Preview')?>" class="ccm-preview" /></a>
						<div class="ccm-theme-name" ><a target="_blank" href="<?php echo $availableTheme->getThemeURL() ?>"><?php echo $availableTheme->getThemeName() ?></a></div>
					</li>
				<?php  } ?> 
				</ul>
			</div>
		</div>			
	<?php  } ?> 	
