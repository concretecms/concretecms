<?
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');
$tp = new TaskPermission();
if ($tp->canInstallPackages()) {
	$mi = Marketplace::getInstance();
}
$pkgArray = Package::getInstalledList();?>

<?
if ($this->controller->getTask() == 'install_package' && $showInstallOptionsScreen && $tp->canInstallPackages()) { ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Install %s', $pkg->getPackageName()), false, 'span10 offset1', false);?>
<form method="post" action="<?=$this->action('install_package', $pkg->getPackageHandle())?>">
<?=Loader::helper('validation/token')->output('install_options_selected')?>
<div class="ccm-pane-body">
<?=Loader::packageElement('dashboard/install', $pkg->getPackageHandle())?>
<? if ($pkg->allowsFullContentSwap()) { ?>
	<h4><?=t('Clear this Site?')?></h4>
	<p><?=t('%s can fully clear you website of all existing content and install its own custom content in its place. If you\'re installing a theme for the first time you may want to do this. Clear all site content?', $pkg->getPackageName())?></p>
	<? $u = new User(); ?>
	<? if ($u->isSuperUser()) {
		$disabled = ''; ?>
	<div class="alert-message warning"><p><?=t('This will clear your home page, uploaded files and any content pages out of your site completely. It will completely reset your site and any content you have added will be lost.')?></p></div>
	<? } else { 
		$disabled = 'disabled';?>
	<div class="alert-message info"><p><?=t('Only the %s user may reset the site\'s content.', USER_SUPER)?></p></div>
	<? } ?>
	<div class="clearfix">
	<label><?=t("Swap Site Contents")?></label>
	<div class="input">
		<ul class="inputs-list">
			<li><label><input type="radio" name="pkgDoFullContentSwap" value="0" checked="checked" <?=$disabled?> /> <span><?=t('No. Do <strong>not</strong> remove any content or files from this website.')?></span></label></li>
			<li><label><input type="radio" name="pkgDoFullContentSwap" value="1" <?=$disabled?> /> <span><?=t('Yes. Reset site content with the content found in this package')?></span></label></li>
		</ul>
	</div>
	</div>
<? } ?>
</div>
<div class="ccm-pane-footer">
	<a href="<?=$this->url('/dashboard/extend/install')?>" class="btn"><?=t('Cancel')?></a>
	<input type="submit" value="<?=t('Install %s', $pkg->getPackageName())?>" class="btn primary ccm-button-right" />
</div>
</form>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>


<? } else if ($this->controller->getTask() == 'uninstall' && $tp->canUninstallPackages()) { ?>
<?
	$removeBTConfirm = t('This will remove all elements associated with the %s package. This cannot be undone. Are you sure?', $pkg->getPackageHandle());
?>
<form method="post" class="form-stacked" id="ccm-uninstall-form" action="<?=$this->action('do_uninstall_package')?>" onsubmit="return confirm('<?=$removeBTConfirm?>')">

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Uninstall Package'), false, 'span10 offset1', false);?>
<div class="ccm-pane-body">
	
	<?=$valt->output('uninstall')?>
	<input type="hidden" name="pkgID" value="<?=$pkg->getPackageID()?>" />
	
	<h3><?=t('Items To Uninstall')?></h3>
	
	<p><?=t('Uninstalling %s will remove the following data from your system.', $pkg->getPackageName())?></p>
		
		<? foreach($items as $k => $itemArray) { 
			if (count($itemArray) == 0) {
				continue;
			}
			?>
			<h5><?=$text->unhandle($k)?></h5>
			<? foreach($itemArray as $item) { ?>
				<?=$pkg->getItemName($item)?><br/>
			<? } ?>
				
		<? } ?>
		<br/>

		<div class="clearfix">
		<h3><?=t('Move package to trash directory on server?')?></h3>
		<ul class="inputs-list">
		<li><label><?=Loader::helper('form')->checkbox('pkgMoveToTrash', 1)?>
		<span><?=t('Yes, remove the package\'s directory from the installation directory.')?></span></label>
		</li>
		</ul>
		</div>
		
		
		<? @Loader::packageElement('dashboard/uninstall', $pkg->getPackageHandle()); ?>
				
		
</div>
<div class="ccm-pane-footer">
<? print $ch->submit(t('Uninstall'), 'ccm-uninstall-form', 'right', 'error'); ?>
<? print $ch->button(t('Cancel'), $this->url('/dashboard/extend/install', 'inspect_package', $pkg->getPackageID()), ''); ?>
</div>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
</form>

<? 
} else { 

	function sortAvailableArray($obj1, $obj2) {
		$name1 = $obj1->getPackageName();
		$name2 = $obj2->getPackageName();
		return strcasecmp($name1, $name2);
	}
	
	// grab the total numbers of updates.
	// this consists of 
	// 1. All packages that have greater pkgAvailableVersions than pkgVersion
	// 2. All packages that have greater pkgVersion than getPackageCurrentlyInstalledVersion
	$local = array();
	$remote = array();
	$pkgAvailableArray = array();
	if ($tp->canInstallPackages()) { 
		$local = Package::getLocalUpgradeablePackages();
		$remote = Package::getRemotelyUpgradeablePackages();
	}
	
	// now we strip out any dupes for the total
	$updates = 0;
	$localHandles = array();
	foreach($local as $_pkg) {
		$updates++;
		$localHandles[] = $_pkg->getPackageHandle();
	}
	foreach($remote as $_pkg) {
		if (!in_array($_pkg->getPackageHandle(), $localHandles)) {
			$updates++;
		}
	}
	if ($tp->canInstallPackages()) { 
		foreach(Package::getAvailablePackages() as $_pkg) {
			$_pkg->setupPackageLocalization();
			$pkgAvailableArray[] = $_pkg;
		}
	}
	

	$thisURL = $this->url('/dashboard/extend/install');
	$availableArray = $pkgAvailableArray;
	usort($availableArray, 'sortAvailableArray');
	
	/* Load featured add-ons from the marketplace.
	 */
	Loader::model('collection_attributes');
	$db = Loader::db();
	
	if(ENABLE_MARKETPLACE_SUPPORT && $tp->canInstallPackages()){
		$purchasedBlocksSource = Marketplace::getAvailableMarketplaceItems();		
	}else{
		$purchasedBlocksSource = array();
	}
	
	$skipHandles = array();
	foreach($availableArray as $ava) {
		foreach($purchasedBlocksSource as $pi) {
			if ($pi->getHandle() == $ava->getPackageHandle()) {
				$skipHandles[] = $ava->getPackageHandle();
			}
		}
	}
	
	$purchasedBlocks = array();
	foreach($purchasedBlocksSource as $pb) {
		if (!in_array($pb->getHandle(), $skipHandles)) {
			$purchasedBlocks[] = $pb;
		}
	}
	
	
	if (is_object($pkg)) { ?>
	
		<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Inspect Package'), false, 'span10 offset1', false);?>
		
		<div class="ccm-pane-body">
			<table class="table table-bordered table-striped">
			<tr>
				<td class="ccm-marketplace-list-thumbnail"><img src="<?=$ci->getPackageIconURL($pkg)?>" /></td>
				<td class="ccm-addon-list-description" style="width: 100%"><h3><?=$pkg->getPackageName()?> - <?=$pkg->getPackageVersion()?></h3><?=$pkg->getPackageDescription()?></td>
			</tr>				
			</table>
		
			<?
			
			$items = $pkg->getPackageItems();
			$blocks = array();
			if (isset($items['block_types']) && is_array($items['block_types'])) {
				$blocks = $items['block_types'];
			}
			
			if (count($blocks) > 0) { ?>
				<h5><?=t("Block Types")?></h5>
				<ul id="ccm-block-type-list">
				<? foreach($blocks as $bt) {
					$btIcon = $ci->getBlockTypeIconURL($bt);?>
					<li class="ccm-block-type ccm-block-type-available">
						<a style="background-image: url(<?=$btIcon?>)" class="ccm-block-type-inner" href="<?=$this->url('/dashboard/blocks/types', 'inspect', $bt->getBlockTypeID())?>"><?=t($bt->getBlockTypeName())?></a>
						<div class="ccm-block-type-description"  id="ccm-bt-help<?=$bt->getBlockTypeID()?>"><?=t($bt->getBlockTypeDescription())?></div>
					</li>
				<? } ?>
				</ul>

			<? } ?>

			</div>
			<div class="ccm-pane-footer">
			<? $tp = new TaskPermission();
			if ($tp->canUninstallPackages()) {  ?>
				<? print $ch->button(t('Uninstall Package'), $this->url('/dashboard/extend/install', 'uninstall', $pkg->getPackageID()), 'right'); ?>
			<? } ?>
				<a href="<?=$this->url('/dashboard/extend/install')?>" class="btn"><?=t('Back to Add Functionality')?></a>			
			</div>
			<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
	<?
	
	 } else { ?>
		
		<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Functionality'), t('Install custom add-ons or those downloaded from the concrete5.org marketplace.'), 'span10 offset1');?>
			
		<? if (is_object($installedPKG) && $installedPKG->hasInstallPostScreen()) { ?>
	
			<div style="display: none">
			<div id="ccm-install-post-notes"><div class="ccm-ui"><?=Loader::element('dashboard/install_post', false, $installedPKG->getPackageHandle())?>
			<div class="dialog-buttons">
				<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeAll()" class="btn ccm-button-right"><?=t('Ok')?></a>
			</div>
			</div>
			</div>
			</div>
			
			<script type="text/javascript">
			$(function() { 
				$('#ccm-install-post-notes').dialog({width: 500, modal: true, height: 400, title: "<?=t('Installation Notes')?>", buttons:[{}], 'open': function() {
					$(this).parent().find('.ui-dialog-buttonpane').addClass("ccm-ui").html('');
					$(this).find('.dialog-buttons').appendTo($(this).parent().find('.ui-dialog-buttonpane'));
					$(this).find('.dialog-buttons').remove();
				}});
			});	
			</script>
		<? } ?>
		
		<h3><?=t('Currently Installed')?></h3>
		<? if (count($pkgArray) > 0) { ?>
			
			<? if ($updates > 0) { ?>
				<div class="block-message alert-message info">
				<h4><?=t('Add-On updates are available!')?></h4>
				<? if ($updates == 1) { ?>
					<p><?=t('There is currently <strong>1</strong> update available.')?></p>
				<? } else { ?>
					<p><?=t('There are currently <strong>%s</strong> updates available.', $updates)?></p>
				<? } ?>
				<div class="alert-actions"><a class="small btn" href="<?=$this->url('/dashboard/extend/update')?>"><?=t('Update Add-Ons')?></a></div>
				</div>
			<? } ?>

			<table class="table table-bordered table-striped">
		
			<?	foreach ($pkgArray as $pkg) { ?>
				<tr>
					<td class="ccm-marketplace-list-thumbnail"><img src="<?=$ci->getPackageIconURL($pkg)?>" /></td>
					<td class="ccm-addon-list-description"><h3><?=$pkg->getPackageName()?> - <?=$pkg->getPackageVersion()?></h3><?=$pkg->getPackageDescription()?>

					</td>
					<td class="ccm-marketplace-list-install-button"><?=$ch->button(t("Edit"), View::url('/dashboard/extend/install', 'inspect_package', $pkg->getPackageID()), "")?></td>					
				</tr>
			<? } ?>
			</table>

		<? } else { ?>		
			<p><?=t('No packages have been installed.')?></p>
		<? } ?>

		<? if ($tp->canInstallPackages()) { ?>
			<h3><?=t('Awaiting Installation')?></h3>
		<? if (count($availableArray) == 0 && count($purchasedBlocks) == 0) { ?>
			
			<? if (!$mi->isConnected()) { ?>
				<?=t('Nothing currently available to install.')?>
			<? } ?>
			
		<? } else { ?>
	
			<table class="table table-bordered table-striped">
			<? foreach ($purchasedBlocks as $pb) {
				$file = $pb->getRemoteFileURL();
				if (!empty($file)) {?>
				<tr>
					<td class="ccm-marketplace-list-thumbnail"><img src="<?=$pb->getRemoteIconURL()?>" /></td>
					<td class="ccm-addon-list-description"><h3><?=$pb->getName()?></h3>
					<?=$pb->getDescription()?>
					</td>
					<td class="ccm-marketplace-list-install-button"><?=$ch->button(t("Download"), View::url('/dashboard/extend/install', 'download', $pb->getMarketplaceItemID()), "", 'primary')?></td>
				</tr>
				<? } ?>
			<? } ?>
			<?	foreach ($availableArray as $obj) { ?>
				<tr>
					<td class="ccm-marketplace-list-thumbnail"><img src="<?=$ci->getPackageIconURL($obj)?>" /></td>
					<td class="ccm-addon-list-description"><h3><?=$obj->getPackageName()?></h3>
					<?=$obj->getPackageDescription()?></td>
					<td class="ccm-marketplace-list-install-button"><?=$ch->button(t("Install"), $this->url('/dashboard/extend/install','install_package', $obj->getPackageHandle()), "");?></td>
				</tr>
			<? } ?>
			</table>
	
	
			<? } ?>
		
		<?
		if (is_object($mi) && $mi->isConnected()) { ?>

			<h3><?=t("Project Page")?></h3>
			<p><?=t('Your site is currently connected to the concrete5 community. Your project page URL is:')?><br/>
			<a href="<?=$mi->getSitePageURL()?>"><?=$mi->getSitePageURL()?></a></p>

		<? } else if (is_object($mi) && $mi->hasConnectionError()) { ?>
			
			<?=Loader::element('dashboard/marketplace_connect_failed');?>
		

		<? } else if ($tp->canInstallPackages() && ENABLE_MARKETPLACE_SUPPORT == true) { ?>

			<div class="well" style="padding:10px 20px;">
				<h3><?=t('Connect to Community')?></h3>
				<p><?=t('Your site is not connected to the concrete5 community. Connecting lets you easily extend a site with themes and add-ons.')?></p>
				<p><a class="btn success" href="<?=$this->url('/dashboard/extend/connect', 'register_step1')?>"><?=t("Connect to Community")?></a></p>
			</div>
		
		<? } ?>
	<? } ?>
<? } 

} ?>
