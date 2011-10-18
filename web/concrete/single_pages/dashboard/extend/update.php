<?
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');
$tp = new TaskPermission();
if ($tp->canInstallPackages()) {
	$mi = Marketplace::getInstance();
}
$pkgArray = Package::getInstalledList();

if (!$tp->canInstallPackages()) { ?>
	<p class="ccm-error"><?=t('You do not have access to download themes or add-ons from the marketplace.')?></p>
<? } else if (!$mi->isConnected()) { ?>
	<? Loader::element('dashboard/marketplace_connect_failed')?>
<? } else {
	$pkgAvailableArray = Package::getLocalUpgradeablePackages();
	$thisURL = $this->url('/dashboard/install', 'update');
	
	if (count($pkgAvailableArray) > 0) { 
	
	?>
	
	<h1><span><?=t('Downloaded and Ready to Install')?></span></h1>
	
	
	<div class="ccm-dashboard-inner">
	<? foreach ($pkgAvailableArray as $pkg) {  ?>
		<div class="ccm-addon-list">
			<table cellspacing="0" cellpadding="0" border="0">		
			<tr>
				<td class="ccm-installed-items-icon"><img src="<?=$ci->getPackageIconURL($pkg)?>" /></td>
				<td class="ccm-addon-list-description"><h3><?=$pkg->getPackageName()?></a></h3><?=$pkg->getPackageDescription()?>
				<br/><br/>
				<strong><?=t('Current Version: %s', $pkg->getPackageCurrentlyInstalledVersion())?></strong><br/>
				<strong><?=t('New Version: %s', $pkg->getPackageVersion())?></strong><br/>
				</td>
				<td><?=$ch->button(t("Update"), View::url('/dashboard/install', 'update', $pkg->getPackageHandle()), "right")?></td>					
			</tr>
			</table>
			</div>
		<? } ?>			
	</div>

	<? } ?>

<? if (ENABLE_MARKETPLACE_SUPPORT) { ?>

<h1><span><?=t('Available for Download')?></span></h1>


<div class="ccm-dashboard-inner">
	<? if (!$mi->isConnected()) { ?>
	<div class="ccm-addon-marketplace-account">
		<? Loader::element('dashboard/marketplace_connect_failed'); ?>	
	</div>
	
	<? } ?>
	

	<h2><?=t('The Following Updates are Available')?></h2>
	
	<?
	$i = 0;
	Loader::model('marketplace_remote_item');
	foreach ($pkgArray as $pkg) { 
		if (!is_object($pkg)) {
			continue;
		}
		if ($pkg->isPackageInstalled() && version_compare($pkg->getPackageVersion(), $pkg->getPackageVersionUpdateAvailable(), '<')) { 
			$i++;
			
			$rpkg = MarketplaceRemoteItem::getByHandle($pkg->getPackageHandle());
			
			?>
			<div class="ccm-addon-list">
			<table cellspacing="0" cellpadding="0" border="0" style="width: auto !important">		
			<tr>
				<td valign="top" class="ccm-installed-items-icon"><img src="<?=$ci->getPackageIconURL($pkg)?>" /></td>
				<td valign="top" class="ccm-addon-list-description" style="width: 100%"><h3><?=$pkg->getPackageName()?></a></h3><?=$pkg->getPackageDescription()?>
				<br/><br/>
				<strong><?=t('Current Version: %s', $pkg->getPackageVersion())?></strong><br/>
				<strong><?=t('New Version: %s', $pkg->getPackageVersionUpdateAvailable())?></strong><br/>
				<a target="_blank" href="<?=$rpkg->getRemoteURL()?>"><?=t('More Information')?></a>
				</td>
				<td valign="top"><?=$ch->button(t("Download and Install"), View::url('/dashboard/install', 'prepare_remote_upgrade', $rpkg->getMarketplaceItemID()), "right")?></td>					
			</tr>
			</table>
			</div>
		<? } ?>			
	<? }
		
		if ($i == 0) { ?>
			
			<p><?=t('There are no updates for your add-ons currently available from the marketplace.')?></p>
			
			
		<? } ?>
	


</div>

<? } ?>

<? 
}