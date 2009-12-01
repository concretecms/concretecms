<?  defined('C5_EXECUTE') or die(_("Access Denied."));

$ch = Loader::helper('concrete/interface');

//marketplace
if (ENABLE_MARKETPLACE_SUPPORT) {
	$themesHelper = Loader::helper('concrete/marketplace/themes'); 
	$availableThemes=$themesHelper->getPreviewableList();
} else {
	$availableThemes=array();
}
?>

<h2><?=t('Themes')?></h2> 

	<? if( !count($availableThemes) ){ ?>
		<div><?=t('Unable to connect to the marketplace.')?></div>
	<? }else{ ?>
		<div class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?=ceil(count($availableThemes)/4)?>" >
			<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
			<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>
						
			<div class="ccm-scroller-inner">
				<ul id="ccm-select-marketplace-theme" style="width: <?=count($availableThemes) * 132?>px">			
				<? foreach($availableThemes as $availableTheme){ ?>
					<li class="themeWrap">
						<span class="ccm-button-marketplace-install"><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED.'/package_install?type=theme&install=1&cID='.$availableTheme->getRemoteCollectionID()?>" title="<?=t('Install theme')?>"><img src="<?=$availableTheme->getThemeThumbnail() ?>" /></a></span>
							<a title="<?=t('Preview')?>" onclick="ccm_previewMarketplaceTheme(<?=$_REQUEST['cID']?>, <?=intval($availableTheme->getRemoteCollectionID())?>,'<?=addslashes($availableTheme->getThemeName()) ?>','<?=addslashes($availableTheme->getThemeHandle()) ?>')" href="javascript:void(0)" class="preview">
							<img src="<?=ASSETS_URL_IMAGES?>/icons/magnifying.png" alt="<?=t('Preview')?>" class="ccm-preview" /></a>
						<div class="ccm-theme-name" ><a target="_blank" href="<?=$availableTheme->getThemeURL() ?>"><?=$availableTheme->getThemeName() ?></a></div>
					</li>
				<? } ?> 
				</ul>
			</div>
		</div>			
	<? } ?> 	
