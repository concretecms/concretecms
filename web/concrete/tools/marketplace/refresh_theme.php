<?  defined('C5_EXECUTE') or die("Access Denied.");

$ch = Loader::helper('concrete/interface');
$tp = new TaskPermission();

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

<h2><?=t('Themes')?></h2> 
	<? if (!$tp->canInstallPackages()) { ?>
		<div><?=t('You do not have permission to connect to the marketplace.')?></div>
	<? } else if( !count($availableThemes) ){ ?>
		<div><?=t('Unable to connect to the marketplace.')?></div>
	<? }else{ ?>
		<div class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?=ceil(count($availableThemes)/4)?>" >
			<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
			<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?=ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>
						
			<div class="ccm-scroller-inner">
				<ul id="ccm-select-marketplace-theme" style="width: <?=count($availableThemes) * 132?>px">			
				<? foreach($availableThemes as $availableTheme){ ?>
					<li class="themeWrap">
						<span class="ccm-button-marketplace-install"><a href="javascript:void(0)" onclick="ccm_getMarketplaceItem({mpID: '<?=$availableTheme->getMarketplaceItemID()?>', onComplete: function() {ccm_marketplaceRefreshInstalledThemes()}})" title="<?=t('Install theme')?>"><img src="<?=$availableTheme->getRemoteListIconURL() ?>" width="97" height="97" /></a></span>
							<a title="<?=t('Preview')?>" onclick="ccm_previewMarketplaceTheme(<?=$_REQUEST['cID']?>, <?=intval($availableTheme->getRemoteCollectionID())?>,'<?=addslashes($availableTheme->getName()) ?>','<?=addslashes($availableTheme->getHandle()) ?>')" href="javascript:void(0)" class="preview">
							<img src="<?=ASSETS_URL_IMAGES?>/icons/magnifying.png" alt="<?=t('Preview')?>" class="ccm-preview" /></a>
						<div class="ccm-theme-name" ><a target="_blank" href="<?=$availableTheme->getRemoteURL() ?>"><?=$availableTheme->getName() ?></a></div>
					</li>
				<? } ?> 
				</ul>
			</div>
		</div>			
	<? } ?> 	
