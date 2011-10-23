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
if ($this->controller->getTask() == 'uninstall' && $tp->canUninstallPackages()) { ?>
<?
	$removeBTConfirm = t('This will remove all elements associated with the %s package. This cannot be undone. Are you sure?', $pkg->getPackageHandle());
?>
<form method="post" class="form-stacked" id="ccm-uninstall-form" action="<?=$this->action('do_uninstall_package')?>" onsubmit="return confirm('<?=$removeBTConfirm?>')">

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Uninstall Package'), false, 'span12 offset2', false);?>
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
		<span><?=t('Yes, remove the package\'s directory from of the installation directory.')?></span></label>
		</li>
		</ul>
		</div>
		
		
		<? Loader::packageElement('dashboard/uninstall', $pkg->getPackageHandle()); ?>
				
		
</div>
<div class="ccm-pane-footer">
<? print $ch->submit(t('Uninstall Package'), 'ccm-uninstall-form', '', 'error'); ?>
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
		$pkgAvailableArray = Package::getAvailablePackages();
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
	
		<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Inspect Package'), false, 'span12 offset2');?>

			<ul class="breadcrumb"><li><a href="<?=$this->url('/dashboard/extend/install')?>">&lt; <?=t('Return to Add Functionality')?></a></li></ul>

			<table class="zebra-striped">
			<tr>
				<td class="ccm-marketplace-list-thumbnail"><img src="<?=$ci->getPackageIconURL($pkg)?>" /></td>
				<td class="ccm-addon-list-description" style="width: 100%"><h3><?=$pkg->getPackageName()?> - <?=$pkg->getPackageVersion()?></a></h3><?=$pkg->getPackageDescription()?></td>
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
						<a style="background-image: url(<?=$btIcon?>)" class="ccm-block-type-inner" href="<?=$this->url('/dashboard/blocks/types', 'inspect', $bt->getBlockTypeID())?>"><?=$bt->getBlockTypeName()?></a>
						<div class="ccm-block-type-description"  id="ccm-bt-help<?=$bt->getBlockTypeID()?>"><?=$bt->getBlockTypeDescription()?></div>
					</li>
				<? } ?>
				</ul>

			<? } ?>

			<? 
			
			$tp = new TaskPermission();
			if ($tp->canUninstallPackages()) { 
			
				$buttons[] = $ch->button(t('Uninstall Package'), $this->url('/dashboard/extend/install', 'uninstall', $pkg->getPackageID()), 'left');
				print $ch->buttons($buttons); 

			} ?>
			
			<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
	<?
	
	 } else { ?>
		
		<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Functionality'), t('Install custom add-ons or those downloaded from the concrete5.org marketplace.'), 'span12 offset2');?>
			
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

			<table class="zebra-striped">
		
			<?	foreach ($pkgArray as $pkg) { ?>
				<tr>
					<td class="ccm-marketplace-list-thumbnail"><img src="<?=$ci->getPackageIconURL($pkg)?>" /></td>
					<td class="ccm-addon-list-description"><h3><?=$pkg->getPackageName()?> - <?=$pkg->getPackageVersion()?></a></h3><?=$pkg->getPackageDescription()?>

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
	
			<table class="zebra-striped">
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
		<? } ?>		
	<? } ?>
<? } ?>