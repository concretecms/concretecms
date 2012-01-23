<?
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');
$tp = new TaskPermission();
if ($tp->canInstallPackages()) {
	$mi = Marketplace::getInstance();
}

$pkgRemote = array();
$pkgLocal = array();
if (ENABLE_MARKETPLACE_SUPPORT) {
	$pkgArray = Package::getInstalledList();
	foreach($pkgArray as $pkg) {
		if ($pkg->isPackageInstalled() && version_compare($pkg->getPackageVersion(), $pkg->getPackageVersionUpdateAvailable(), '<')) { 
			$pkgRemote[] = $pkg;
		}
	}
}
$pkgAvailableArray = Package::getLocalUpgradeablePackages();
foreach($pkgAvailableArray as $pkg) {
	if (!in_array($pkg, $pkgRemote)) {
		$pkgLocal[] = $pkg;
	}
}


if (!$tp->canInstallPackages()) { ?>
	<p class="block-message alert-message error"><?=t('You do not have access to download themes or add-ons from the marketplace.')?></p>
<? } else { ?>

		<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Update Add-Ons'));?>
		<? if (count($pkgLocal) == 0 && count($pkgRemote) == 0) { ?>
			<p><?=t('No updates for your add-ons are available.')?></p>
		<? } else { ?>

			<table>
			<? foreach($pkgRemote as $pkg) { 

				$rpkg = MarketplaceRemoteItem::getByHandle($pkg->getPackageHandle());
			?>
			
				<tr>
					<td class="ccm-marketplace-list-thumbnail" rowspan="2"><img src="<?=$ci->getPackageIconURL($pkg)?>" /></td>
					<td class="ccm-addon-list-description"><h3><?=$pkg->getPackageName()?></h3><p><?=$pkg->getPackageDescription()?></p>
					<p><strong><?=t('New Version: %s. Upgrading from: %s.', $pkg->getPackageVersionUpdateAvailable(), $pkg->getPackageVersion())?></p>

					</td>
					<? if (!is_object($rpkg)) { ?>
						<td class="ccm-marketplace-list-install-button"><input class="btn" disabled="disabled" type="button" value="<?=t('More Information')?>" /> <input class="btn primary" disabled="disabled" type="button" value="<?=t('Download and Install')?>" />
					<? } else { ?>
						<td class="ccm-marketplace-list-install-button"><a class="btn" target="_blank" href="<?=$rpkg->getRemoteURL()?>"><?=t('More Information')?></a> <?=$ch->button(t("Download and Install"), View::url('/dashboard/extend/update', 'prepare_remote_upgrade', $rpkg->getMarketplaceItemID()), "", "primary")?></td>					
					<? } ?>
				</tr>
				<? if (is_object($rpkg)) { ?>
				<tr>
					<td colspan="2" style="border-top: 0px">
						<? $versionHistory = $rpkg->getVersionHistory();?>
						<? if (trim($versionHistory) != '') { ?>
							<div class="ccm-marketplace-update-changelog">
								<h6><?=t('Version History')?></h6>
								<?=$versionHistory?>
							</div>
							<div class="ccm-marketplace-item-information-more">
								<a href="javascript:void(0)" onclick="ccm_marketplaceUpdatesShowMore(this)"><?=t('More Details')?></a>
							</div>
						<? } ?>
					</td>
				</tr>
				<? } else { ?>
				<tr>
					<td colspan="2" style="border-top: 0px">
						<div class="block-message alert-message error"><p><?=t('Unable to locate this add-on on concrete5.org')?></p></div>
					</td>
				</tr>
				<? } ?>		
			<? }
			
			foreach($pkgLocal as $pkg) { ?>
			
				<tr>
					<td class="ccm-marketplace-list-thumbnail" rowspan="2"><img src="<?=$ci->getPackageIconURL($pkg)?>" /></td>
					<td class="ccm-addon-list-description"><h3><?=$pkg->getPackageName()?></h3><p><?=$pkg->getPackageDescription()?></p>
					<p><strong><?=t('New Version: %s. Upgrading from: %s.', $pkg->getPackageVersion(), $pkg->getPackageCurrentlyInstalledVersion())?></p>
					</td>
					<td class="ccm-marketplace-list-install-button"><?=$ch->button(t("Update Add-On"), View::url('/dashboard/extend/update', 'do_update', $pkg->getPackageHandle()), "", "primary")?></td>					
				</tr>
				<tr>
					<td colspan="2" style="border-top: 0px">
						<? $versionHistory = $pkg->getChangelogContents();?>
						<? if (trim($versionHistory) != '') { ?>
							<div class="ccm-marketplace-update-changelog">
								<h6><?=t('Version History')?></h6>
								<?=$versionHistory?>
							</div>
							<div class="ccm-marketplace-item-information-more">
								<a href="javascript:void(0)" onclick="ccm_marketplaceUpdatesShowMore(this)"><?=t('More Details')?></a>
							</div>
						<? } ?>
					</td>
				</tr>
				
			<? } ?>		
			
			</table>
			
		<? } ?>
		<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();
		?>

<? } ?>