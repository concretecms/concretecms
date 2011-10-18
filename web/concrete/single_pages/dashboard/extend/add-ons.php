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


if ($this->controller->getTask() == 'browse') { ?>

<h1><span><?=t("Browse the Marketplace")?></span></h1>
<div class="ccm-dashboard-inner">
<? 
	if (!$tp->canInstallPackages()) { ?>
	
		<p class="ccm-error"><?=t('You do not have access to download themes or add-ons from the marketplace.')?></p>
	<? } else if (!$mi->isConnected()) { ?>
		<? Loader::element('dashboard/marketplace_connect_failed')?>
	<? } else { ?>
		
		<form method="get">
			
			<div style="border-bottom: 1px dotted #dedede; padding-bottom: 0px; margin-bottom: 8px"><h3><?=t('Search')?>
				<?=$form->text('marketplaceRemoteItemKeywords', array('style' => 'width: 100px'))?>
				<?=t('in')?>
				<?=$form->select('marketplaceRemoteItemSetID', $sets, $selectedSet)?>
				<?=$form->submit('submit', t('Search'))?>
				</h3>
			</div>
			
			<? if ($list->getTotal() > 0) { ?>
				<?=$list->displaySummary()?>
					
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
					<?php 
					$numCols=3;
					$colCount=0;
					foreach($items as $item){ 
						if($colCount==$numCols){
							echo '</tr><tr>';
							$colCount=0;
						}
						if ($item->purchaseRequired()) {
							$buttonText = t('Purchase');
								$buttonAction = 'javascript:ccm_getMarketplaceItem({mpID: \'' . $item->getMarketplaceItemID() . '\'})';
						} else {
							$buttonText = t('Install');
							if ($type == 'themes') {
								$buttonAction = 'javascript:ccm_getMarketplaceItem({mpID: \'' . $item->getMarketplaceItemID() . '\', onComplete: function() {window.location.href=\'' . $this->url('/dashboard/pages/themes') . '\'}})';
							} else {
								$buttonAction = 'javascript:ccm_getMarketplaceItem({mpID: \'' . $item->getMarketplaceItemID() . '\', onComplete: function() {window.location.href=\'' . $this->url('/dashboard/install') . '\'}})';					
							}
						}
						?>
						<td valign="top" width="<?php echo round(100/$numCols)?>%" style="padding-bottom: 20px"> 
							<div><? if ($type == 'themes') { ?><a title="<?php echo t('Preview')?>" onclick="ccm_previewMarketplaceTheme(1, <?php echo intval($item->getRemoteCollectionID())?>,'<?php echo addslashes($item->getName()) ?>','<?php echo addslashes($item->getHandle()) ?>')" 
								href="javascript:void(0)" class="preview"><? } ?><img style="margin-bottom: 8px" src="<?php echo $item->getRemoteIconURL() ?>" /><? if ($type == 'themes') { ?></a><? } ?></div>
							<h2><?php echo $item->getName() ?>
							<? if ($type == 'themes') { ?>
							<a title="<?php echo t('Preview')?>" onclick="ccm_previewMarketplaceTheme(1, <?php echo intval($item->getRemoteCollectionID())?>,'<?php echo addslashes($item->getName()) ?>','<?php echo addslashes($item->getHandle()) ?>')" 
								href="javascript:void(0)" class="preview"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/magnifying.png" alt="<?php echo t('Preview')?>" /></a>
							<? } ?>
							</h2>						
							<div><?php echo $item->getDescription() ?></div>
							<div style="margin-top: 8px"><strong><?=t('Price')?></strong> <?=((float) $item->getPrice() == 0) ? t('Free!') : $item->getPrice()?></div>
							<div style="margin-top: 8px">
							<?=$ch->button_js(t('More Information'), 'window.open(\'' . $item->getRemoteURL() . '\')', 'left');?>
							<?=$ch->button_js($buttonText, $buttonAction, 'left')?>
							</div>
						</td>
					<?php   $colCount++;
					}
					for($i=$colCount;$i<$numCols;$i++){
						echo '<td>&nbsp;</td>'; 
					} 
					?>
					</tr>
				</table>
			
				<? $list->displayPaging()?>
			<? } else { ?>
				<p><?=t('No results found.')?></p>
			<? } ?>
		
		</form>

	<? } ?>

	<div class="ccm-spacer">&nbsp;</div>

</div>

<? } else if ($this->controller->getTask() == 'uninstall' && $tp->canUninstallPackages()) { ?>

<div style="width: 760px">
<h1><span><?=t("Uninstall Package")?></span></h1>
<div class="ccm-dashboard-inner">
	
	<?
		$removeBTConfirm = t('This will remove all elements associated with the %s package. This cannot be undone. Are you sure?', $pkg->getPackageHandle());
	?>
	
	<form method="post" id="ccm-uninstall-form" action="<?=$this->action('do_uninstall_package')?>" onsubmit="return confirm('<?=$removeBTConfirm?>')">
	<?=$valt->output('uninstall')?>
	<input type="hidden" name="pkgID" value="<?=$pkg->getPackageID()?>" />
	
	<h2><?=t('Items To Uninstall')?></h2>
	
	<p><?=t('Uninstalling %s will remove the following data from your system.', $pkg->getPackageName())?></p>
		
		<? foreach($items as $k => $itemArray) { 
			if (count($itemArray) == 0) {
				continue;
			}
			?>
			<h3><?=$text->unhandle($k)?></h3>
			
			<? foreach($itemArray as $item) { ?>
				<?=$pkg->getItemName($item)?><br/>			
			<? } ?>
			
			<br/>
			
		<? } ?>


		<h2><?=t('Move package to trash directory on server?')?></h2>
		<p><?=Loader::helper('form')->checkbox('pkgMoveToTrash', 1)?> <?=Loader::helper('form')->label('pkgMoveToTrash', t('Yes, remove the package\'s directory from of the installation directory.'))?></p>
		
		
		<? Loader::packageElement('dashboard/uninstall', $pkg->getPackageHandle()); ?>
		
		
<?
		$u = new User();
		$buttons[] = $ch->button(t('Cancel'), $this->url('/dashboard/install', 'inspect_package', $pkg->getPackageID()), 'left');
		$buttons[] = $ch->submit(t('Uninstall Package'), 'ccm-uninstall-form', 'right');
		
		print $ch->buttons($buttons);
		?>
		
		<div class="ccm-spacer">&nbsp;</div>
		</form>
		
</div>
</div>

<? } else if ($this->controller->getTask() == 'update' && $tp->canInstallPackages()) { 

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
} else { 

	function sortAvailableArray($obj1, $obj2) {
		$name1 = ($obj1 instanceof Package) ? $obj1->getPackageName() : $obj1->getBlockTypeName();
		$name2 = ($obj2 instanceof Package) ? $obj2->getPackageName() : $obj2->getBlockTypeName();
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
	

	$thisURL = $this->url('/dashboard/install');

	$btArray = BlockTypeList::getInstalledList();
	$btAvailableArray = BlockTypeList::getAvailableList();
	
	$coreBlockTypes = array();
	$webBlockTypes = array();
	
	foreach($btArray as $_bt) {
		if ($_bt->getPackageID() == 0) {
			if ($_bt->isCoreBlockType()) {
				$coreBlockTypes[] = $_bt;
			} else {
				$webBlockTypes[] = $_bt;
			}
		}
	}
	$availableArray = array_merge($btAvailableArray, $pkgAvailableArray);
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
	
	// now we iterate through the purchased items (NOT BLOCKS, THESE CAN INCLUDE THEMES) list and removed ones already downloaded
	// This really should be made into a more generic object since it's not block types anymore.
	
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
	
		<h1><span><?=$pkg->getPackageName()?></span></h1>
		<div class="ccm-dashboard-inner">
			<img src="<?=$ci->getPackageIconURL($pkg)?>" style="float: right" />
			<div><a href="<?=$this->url('/dashboard/install')?>">&lt; <?=t('Return to Add Functionality')?></a></div><br/>
				
			<h2><?=t('Description')?></h2>
			<p><?=$pkg->getPackageDescription()?></p>
		
			<?
			
			$items = $pkg->getPackageItems();
			$blocks = array();
			if (isset($items['block_types']) && is_array($items['block_types'])) {
				$blocks = $items['block_types'];
			}
			
			if (count($blocks) > 0) { ?>
				<h2><?=t("Block Types")?></h2>
				<? foreach($blocks as $bt) { ?>
	
					<div class="ccm-addon-list">
					<table cellspacing="0" cellpadding="0" border="0">		
					<tr>
						<td class="ccm-installed-items-icon"><img src="<?=$ci->getBlockTypeIconURL($bt)?>" /></td>
						<td class="ccm-addon-list-description"><h3><?=$bt->getBlockTypeName()?></a></h3><?=$bt->getBlockTypeDescription()?></td>
						<td><div style="width: 80px"><?=$ch->button(t("Edit"), View::url('/dashboard/install', 'inspect_block_type', $bt->getBlockTypeID()))?></div></td>					
					</tr>
					</table>
					</div>
				
				<? } ?>		
				<br/><br/>
			<? } ?>
			
			<div class="ccm-spacer">&nbsp;</div>
			
			<? 
			
			$tp = new TaskPermission();
			if ($tp->canUninstallPackages()) { 
			
				$buttons[] = $ch->button(t('Uninstall Package'), $this->url('/dashboard/install', 'uninstall', $pkg->getPackageID()), 'left');
				print $ch->buttons($buttons); 

			} ?>
			
		</div>
		
	<?
	
	} else if (is_object($bt)) { ?>
	
		<h1><span><?=$bt->getBlockTypeName()?></span></h1>
		<div class="ccm-dashboard-inner">
			<img src="<?=$ci->getBlockTypeIconURL($bt)?>" style="float: right" />
			<div><a href="<?=$this->url('/dashboard/install')?>">&lt; <?=t('Return to Add Functionality')?></a></div><br/>
				
			<h2><?=t('Description')?></h2>
			<p><?=$bt->getBlockTypeDescription()?></p>
		
			<h2><?=t('Usage Count')?></h2>
			<p><?=$num?></p>
				
			<? if ($bt->isBlockTypeInternal()) { ?>
			<h2><?=t('Internal')?></h2>
			<p><?=t('This is an internal block type.')?></p>
			<? } ?>
	
			<?
			$buttons[] = $ch->button(t("Refresh"), $this->url('/dashboard/install','refresh_block_type', $bt->getBlockTypeID()), "left");
			$u = new User();
			
			if ($u->isSuperUser()) {
			
				$removeBTConfirm = t('This will remove all instances of the %s block type. This cannot be undone. Are you sure?', $bt->getBlockTypeHandle());
				
				$buttons[] = $ch->button_js(t('Remove'), 'removeBlockType()', 'left');?>
	
				<script type="text/javascript">
				removeBlockType = function() {
					if (confirm('<?=$removeBTConfirm?>')) { 
						location.href = "<?=$this->url('/dashboard/install', 'uninstall_block_type', $bt->getBlockTypeID(), $valt->generate('uninstall'))?>";				
					}
				}
				</script>
	
			<? } else { ?>
				<? $buttons[] = $ch->button_js(t('Remove'), 'alert(\'' . t('Only the super user may remove block types.') . '\')', 'left', 'ccm-button-inactive');?>
			<? }
			
			print $ch->buttons($buttons); ?>
			
		</div>
				
	<? } else { ?>
		
		<!--[if IE 7]>
		<style type="text/css">
		td.ccm-addon-list-description {width: 161px !important}
		</style>
		<![endif]-->
		<div style="width: 720px">
		<div class="ccm-module" style="width: 350px; margin-bottom: 20px">
			
			<h1><span><?=t('Currently Installed')?></span></h1>
			<div class="ccm-dashboard-inner">
			<? if (count($pkgArray) > 0) { ?>
			<h2><?=t('Packages')?></h2>
			
				<?	foreach ($pkgArray as $pkg) { ?>
					<div class="ccm-addon-list">
					<table cellspacing="0" cellpadding="0">		
					<tr>
						<td class="ccm-installed-items-icon"><img src="<?=$ci->getPackageIconURL($pkg)?>" /></td>
						<td class="ccm-addon-list-description"><h3><?=$pkg->getPackageName()?> - <?=$pkg->getPackageVersion()?></a></h3><?=$pkg->getPackageDescription()?>

						</td>
						<td><?=$ch->button(t("Edit"), View::url('/dashboard/install', 'inspect_package', $pkg->getPackageID()), "right")?></td>					
					</tr>
					</table>
					</div>
				<? } ?>				
		
				<br/><br/>
	
			<? } ?>
			
			<? if (count($webBlockTypes) > 0) { ?>
				<h2><?=t('Custom Block Types')?></h2>
				<?	foreach ($webBlockTypes as $bt) { ?>
					<div class="ccm-addon-list">
					<table cellspacing="0" cellpadding="0">		
					<tr>
						<td class="ccm-installed-items-icon"><img src="<?=$ci->getBlockTypeIconURL($bt)?>" /></td>
						<td class="ccm-addon-list-description"><h3><?=$bt->getBlockTypeName()?></a></h3><?=$bt->getBlockTypeDescription()?></td>
						<td><?=$ch->button(t("Edit"), View::url('/dashboard/install', 'inspect_block_type', $bt->getBlockTypeID()), "right")?></td>					
					</tr>
					</table>
					</div>
				<? } ?>
				<br/><br/>
			<? } ?>
			
			<h2><?=t('Core Block Types')?></h2>
			<? 
			if (count($coreBlockTypes) == 0) { ?>
				<p><?=t('No block types have been installed.')?></p>
			<? } else { ?>
			
				<?	foreach ($coreBlockTypes as $bt) { ?>
					<div class="ccm-addon-list">
					<table cellspacing="0" cellpadding="0">		
					<tr>
						<td class="ccm-installed-items-icon"><img src="<?=$ci->getBlockTypeIconURL($bt)?>" /></td>
						<td class="ccm-addon-list-description"><h3><?=$bt->getBlockTypeName()?></a></h3><?=$bt->getBlockTypeDescription()?></td>
						<td><?=$ch->button(t("Edit"), View::url('/dashboard/install', 'inspect_block_type', $bt->getBlockTypeID()), "right")?></td>					
					</tr>
					</table>
					</div>
				<? } ?>				
			<? } ?>
	
			</div>
				
		</div>
		
		<? if ($tp->canInstallPackages()) { ?>
		
		<div class="ccm-module" style="width: 350px; margin-bottom: 20px">
				<? if ($updates > 0) { ?>
				<h1><span><?=t('Updates')?></span></h1>
				<div class="ccm-dashboard-inner">
					<? if ($updates == 1) { ?>
						<?=t('There is currently <strong>1</strong> update available.')?>
					<? } else { ?>
						<?=t('There are currently <strong>%s</strong> updates available.', $updates)?>
					<? } ?>
					<? print $ch->button(t('Update Addons'), $this->url('/dashboard/install', 'update'))?>
					
					<div class="ccm-spacer">&nbsp;</div>
				
				</div>
				
			
			<br/>
			<? } ?>
			

			<h1><span><?=t('New')?></span></h1>
			<div class="ccm-dashboard-inner">
			 
			<? if (ENABLE_MARKETPLACE_SUPPORT) { ?>
					
			<div class="ccm-addon-marketplace-account">
			<? 
			Loader::library('marketplace');
			if ($mi->isConnected()) { ?>				
				<?=t('Your site is currently connected to the concrete5 community.')?><br/><br/>
				<? if (count($purchasedBlocks) == 0) { ?>
					<?=t('There appears to be nothing currently available to install from your <a href="%s" target="_blank">project page</a>.', $mi->getSitePageURL())?><br/><br/>
				<? } ?>
				<?=t('Browse more <a href="%s">add-ons</a> and <a href="%s">themes</a>, and check on your <a href="%s" target="_blank">project page</a>.', $this->url('/dashboard/install/', 'browse', 'addons'), $this->url('/dashboard/install', 'browse', 'themes'), $mi->getSitePageURL())?>
				<br/><br/>
				<a href="<?=$this->url('/dashboard/install', 'update')?>"><?=t("Check for updates &gt;")?></a>
			<?
			
			} else {
				Loader::element('dashboard/marketplace_connect_failed');
			}
			?>
				<div class="ccm-spacer">&nbsp;</div>
			</div>
			
			<? } ?>
			
		<? if (count($availableArray) == 0 && count($purchasedBlocks) == 0) { ?>
			
			<? if (!$mi->isConnected()) { ?>
				<?=t('Nothing currently available to install.')?>
			<? } ?>
			
		<? } else { ?>
	
			<div class="ccm-addon-list-wrapper">
			
			<? if (count($availableArray) > 0) { ?>
			<h2><?=t('Downloaded and Ready to Install')?></h2>
			<? } ?>
			<?	foreach ($availableArray as $obj) { ?>
				<div class="ccm-addon-list">
				<table cellspacing="0" cellpadding="0" border="0">
				<tr>
				<? if (get_class($obj) == "BlockType") { ?>
					<td><img src="<?=$ci->getBlockTypeIconURL($obj)?>" /></td>
					<td class="ccm-addon-list-description"><h3><?=$obj->getBlockTypeName()?></h3>
					<?=$obj->getBlockTypeDescription()?></td>
					<td><?=$ch->button(t("Install"), $this->url('/dashboard/install','install_block_type', $obj->getBlockTypeHandle()), "right");?></td>
				<? } else { ?>
					<td><img src="<?=$ci->getPackageIconURL($obj)?>" /></td>
					<td class="ccm-addon-list-description"><h3><?=$obj->getPackageName()?></h3>
					<?=$obj->getPackageDescription()?></td>
					<td><?=$ch->button(t("Install"), $this->url('/dashboard/install','install_package', $obj->getPackageHandle()), "right");?></td>
				<? } ?>
				</tr>
				</table>
				</div>
			<? } ?>
			
			<br/><Br/>
			<? if (count($purchasedBlocks) > 0) { ?>
			<h2><?=t('Ready to Download')?></h2>
			<? } ?>
	
			<? foreach ($purchasedBlocks as $pb) {
				$file = $pb->getRemoteFileURL();
				if (!empty($file)) {?>
				<div class="ccm-addon-list">
				<table cellspacing="0" cellpadding="0">
				<tr>
					<td><img src="<?=$pb->getRemoteIconURL()?>" /></td>
					<td class="ccm-addon-list-description"><h3><?=$pb->getName()?></h3>
					<?=$pb->getDescription()?>
					</td>
					<td width="120"><?=$ch->button(t("Download"), View::url('/dashboard/install', 'download', $pb->getMarketplaceItemID()), "right")?></td>
				</tr>
				</table>
				</div>
				<? } ?>
			<? } ?>
	
			</div>
	
			<? } ?>
	
			</div>
	
		</div>
	
		</div>
		</div>
		
		<? } ?>
		
		</div>
	
	<? } ?>
<? } ?>