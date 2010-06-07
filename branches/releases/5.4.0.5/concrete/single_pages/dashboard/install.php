<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$valt = Loader::helper('validation/token');
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');
$mi = Marketplace::getInstance();
$pkgArray = Package::getInstalledList();

$tp = new TaskPermission();

if ($this->controller->getTask() == 'browse') { ?>

<h1><span><?php echo t("Browse the Marketplace")?></span></h1>
<div class="ccm-dashboard-inner">
<?php  
	if (!$mi->isConnected()) { ?>
		<?php  Loader::element('dashboard/marketplace_connect_failed')?>
	<?php  } else { ?>
		
		<form method="get">
			
			<div style="border-bottom: 1px dotted #dedede; padding-bottom: 0px; margin-bottom: 8px"><h3><?php echo t('Search')?>
				<?php echo $form->text('marketplaceRemoteItemKeywords', array('style' => 'width: 100px'))?>
				<?php echo t('in')?>
				<?php echo $form->select('marketplaceRemoteItemSetID', $sets, $selectedSet)?>
				<?php echo $form->submit('submit', t('Search'))?>
				</h3>
			</div>
			
			<?php  if ($list->getTotal() > 0) { ?>
				<?php echo $list->displaySummary()?>
					
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
							$buttonAction = 'javascript:window.open(\'' . $item->getRemoteURL() . '\')';
						} else {
							$buttonText = t('Install');
							if ($type == 'themes') {
								$buttonAction = 'javascript:ccm_getMarketplaceItem({mpID: \'' . $item->getMarketplaceItemID() . '\', onComplete: function() {window.location.href=\'' . $this->url('/dashboard/pages/themes') . '\'}})';
							} else {
								$buttonAction = 'javascript:ccm_getMarketplaceItem({mpID: \'' . $item->getMarketplaceItemID() . '\', onComplete: function() {window.location.href=\'' . $this->url('/dashboard/install') . '\'}})';					
							}
						}
						?>
						<td valign="top" width="<?php  echo round(100/$numCols)?>%" style="padding-bottom: 20px"> 
							<div><?php  if ($type == 'themes') { ?><a title="<?php  echo t('Preview')?>" onclick="ccm_previewMarketplaceTheme(1, <?php  echo intval($item->getRemoteCollectionID())?>,'<?php  echo addslashes($item->getName()) ?>','<?php  echo addslashes($item->getHandle()) ?>')" 
								href="javascript:void(0)" class="preview"><?php  } ?><img style="margin-bottom: 8px" src="<?php  echo $item->getRemoteIconURL() ?>" /><?php  if ($type == 'themes') { ?></a><?php  } ?></div>
							<h2><?php  echo $item->getName() ?>
							<?php  if ($type == 'themes') { ?>
							<a title="<?php  echo t('Preview')?>" onclick="ccm_previewMarketplaceTheme(1, <?php  echo intval($item->getRemoteCollectionID())?>,'<?php  echo addslashes($item->getName()) ?>','<?php  echo addslashes($item->getHandle()) ?>')" 
								href="javascript:void(0)" class="preview"><img src="<?php  echo ASSETS_URL_IMAGES?>/icons/magnifying.png" alt="<?php  echo t('Preview')?>" /></a>
							<?php  } ?>
							</h2>						
							<div><?php  echo $item->getDescription() ?></div>
							<div style="margin-top: 8px"><strong><?php echo t('Price')?></strong> <?php echo ((float) $item->getPrice() == 0) ? t('Free!') : $item->getPrice()?></div>
							<div style="margin-top: 8px">
							<?php echo $ch->button_js(t('More Information'), 'window.open(\'' . $item->getRemoteURL() . '\')', 'left');?>
							<?php echo $ch->button_js($buttonText, $buttonAction, 'left')?>
							</div>
						</td>
					<?php    $colCount++;
					}
					for($i=$colCount;$i<$numCols;$i++){
						echo '<td>&nbsp;</td>'; 
					} 
					?>
					</tr>
				</table>
			
				<?php  $list->displayPaging()?>
			<?php  } else { ?>
				<p><?php echo t('No results found.')?></p>
			<?php  } ?>
		
		</form>

	<?php  } ?>

	<div class="ccm-spacer">&nbsp;</div>

</div>

<?php  } else if ($this->controller->getTask() == 'uninstall' && $tp->canUninstallPackages()) { ?>

<div style="width: 760px">
<h1><span><?php echo t("Uninstall Package")?></span></h1>
<div class="ccm-dashboard-inner">
	
	<?php 
		$removeBTConfirm = t('This will remove all elements associated with the %s package. This cannot be undone. Are you sure?', $pkg->getPackageHandle());
	?>
	
	<form method="post" id="ccm-uninstall-form" action="<?php echo $this->action('do_uninstall_package')?>" onsubmit="return confirm('<?php echo $removeBTConfirm?>')">
	<?php echo $valt->output('uninstall')?>
	<input type="hidden" name="pkgID" value="<?php echo $pkg->getPackageID()?>" />
	
	<h2><?php echo t('Items To Uninstall')?></h2>
	
	<p><?php echo t('Uninstalling %s will remove the following data from your system.', $pkg->getPackageName())?></p>
		
		<?php  foreach($items as $k => $itemArray) { 
			if (count($itemArray) == 0) {
				continue;
			}
			?>
			<h3><?php echo $text->unhandle($k)?></h3>
			
			<?php  foreach($itemArray as $item) { ?>
				<?php echo Package::getItemName($item)?><br/>			
			<?php  } ?>
			
			<br/>
			
		<?php  } ?>


		<h2><?php echo t('Move package to trash directory on server?')?></h2>
		<p><?php echo Loader::helper('form')->checkbox('pkgMoveToTrash', 1)?> <?php echo Loader::helper('form')->label('pkgMoveToTrash', t('Yes, remove the package\'s directory from of the installation directory.'))?></p>
		
		
		<?php  Loader::packageElement('dashboard/uninstall', $pkg->getPackageHandle()); ?>
		
		
<?php 
		$u = new User();
		$buttons[] = $ch->button(t('Cancel'), $this->url('/dashboard/install', 'inspect_package', $pkg->getPackageID()), 'left');
		$buttons[] = $ch->submit(t('Uninstall Package'), 'ccm-uninstall-form', 'right');
		
		print $ch->buttons($buttons);
		?>
		
		<div class="ccm-spacer">&nbsp;</div>
		</form>
		
</div>
</div>

<?php  } else if ($this->controller->getTask() == 'update') { 

	$pkgAvailableArray = Package::getLocalUpgradeablePackages();
	$thisURL = $this->url('/dashboard/install', 'update');
	
	if (count($pkgAvailableArray) > 0) { 
	
	?>
	
	<h1><span><?php echo t('Downloaded and Ready to Install')?></span></h1>
	
	
	<div class="ccm-dashboard-inner">
	<?php  foreach ($pkgAvailableArray as $pkg) {  ?>
		<div class="ccm-addon-list">
			<table cellspacing="0" cellpadding="0" border="0">		
			<tr>
				<td class="ccm-installed-items-icon"><img src="<?php echo $ci->getPackageIconURL($pkg)?>" /></td>
				<td class="ccm-addon-list-description"><h3><?php echo $pkg->getPackageName()?></a></h3><?php echo $pkg->getPackageDescription()?>
				<br/><br/>
				<strong><?php echo t('Current Version: %s', $pkg->getPackageCurrentlyInstalledVersion())?></strong><br/>
				<strong><?php echo t('New Version: %s', $pkg->getPackageVersion())?></strong><br/>
				</td>
				<td><?php echo $ch->button(t("Update"), View::url('/dashboard/install', 'update', $pkg->getPackageHandle()), "right")?></td>					
			</tr>
			</table>
			</div>
		<?php  } ?>			
	</div>


<?php  } ?>
<?php  if (ENABLE_MARKETPLACE_SUPPORT) { ?>

<h1><span><?php echo t('Available for Download')?></span></h1>


<div class="ccm-dashboard-inner">
	<?php  if (!$mi->isConnected()) { ?>
	<div class="ccm-addon-marketplace-account">
		<?php  Loader::element('dashboard/marketplace_connect_failed'); ?>	
	</div>
	
	<?php  } ?>
	

	<h2><?php echo t('The Following Updates are Available')?></h2>
	
	<?php 
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
				<td valign="top" class="ccm-installed-items-icon"><img src="<?php echo $ci->getPackageIconURL($pkg)?>" /></td>
				<td valign="top" class="ccm-addon-list-description" style="width: 100%"><h3><?php echo $pkg->getPackageName()?></a></h3><?php echo $pkg->getPackageDescription()?>
				<br/><br/>
				<strong><?php echo t('Current Version: %s', $pkg->getPackageVersion())?></strong><br/>
				<strong><?php echo t('New Version: %s', $pkg->getPackageVersionUpdateAvailable())?></strong><br/>
				<a target="_blank" href="<?php echo $rpkg->getRemoteURL()?>"><?php echo t('More Information')?></a>
				</td>
				<td valign="top"><?php echo $ch->button(t("Download and Install"), View::url('/dashboard/install', 'prepare_remote_upgrade', $rpkg->getMarketplaceItemID()), "right")?></td>					
			</tr>
			</table>
			</div>
		<?php  } ?>			
	<?php  }
		
		if ($i == 0) { ?>
			
			<p><?php echo t('There are no updates for your add-ons currently available from the marketplace.')?></p>
			
			
		<?php  } ?>
	


</div>

<?php  } ?>

<?php  
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
	$local = Package::getLocalUpgradeablePackages();
	$remote = Package::getRemotelyUpgradeablePackages();
	
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
	
	$pkgAvailableArray = Package::getAvailablePackages();


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
	
	if(ENABLE_MARKETPLACE_SUPPORT){
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
	
		<h1><span><?php echo $pkg->getPackageName()?></span></h1>
		<div class="ccm-dashboard-inner">
			<img src="<?php echo $ci->getPackageIconURL($pkg)?>" style="float: right" />
			<div><a href="<?php echo $this->url('/dashboard/install')?>">&lt; <?php echo t('Return to Add Functionality')?></a></div><br/>
				
			<h2><?php echo t('Description')?></h2>
			<p><?php echo $pkg->getPackageDescription()?></p>
		
			<?php 
			
			$items = $pkg->getPackageItems();
			$blocks = array();
			if (isset($items['block_types']) && is_array($items['block_types'])) {
				$blocks = $items['block_types'];
			}
			
			if (count($blocks) > 0) { ?>
				<h2><?php echo t("Block Types")?></h2>
				<?php  foreach($blocks as $bt) { ?>
	
					<div class="ccm-addon-list">
					<table cellspacing="0" cellpadding="0" border="0">		
					<tr>
						<td class="ccm-installed-items-icon"><img src="<?php echo $ci->getBlockTypeIconURL($bt)?>" /></td>
						<td class="ccm-addon-list-description"><h3><?php echo $bt->getBlockTypeName()?></a></h3><?php echo $bt->getBlockTypeDescription()?></td>
						<td><div style="width: 80px"><?php echo $ch->button(t("Edit"), View::url('/dashboard/install', 'inspect_block_type', $bt->getBlockTypeID()))?></div></td>					
					</tr>
					</table>
					</div>
				
				<?php  } ?>		
				<br/><br/>
			<?php  } ?>
			
			<div class="ccm-spacer">&nbsp;</div>
			
			<?php  
			
			$tp = new TaskPermission();
			if ($tp->canUninstallPackages()) { 
			
				$buttons[] = $ch->button(t('Uninstall Package'), $this->url('/dashboard/install', 'uninstall', $pkg->getPackageID()), 'left');
				print $ch->buttons($buttons); 

			} ?>
			
		</div>
		
	<?php 
	
	} else if (is_object($bt)) { ?>
	
		<h1><span><?php echo $bt->getBlockTypeName()?></span></h1>
		<div class="ccm-dashboard-inner">
			<img src="<?php echo $ci->getBlockTypeIconURL($bt)?>" style="float: right" />
			<div><a href="<?php echo $this->url('/dashboard/install')?>">&lt; <?php echo t('Return to Add Functionality')?></a></div><br/>
				
			<h2><?php echo t('Description')?></h2>
			<p><?php echo $bt->getBlockTypeDescription()?></p>
		
			<h2><?php echo t('Usage Count')?></h2>
			<p><?php echo $num?></p>
				
			<?php  if ($bt->isBlockTypeInternal()) { ?>
			<h2><?php echo t('Internal')?></h2>
			<p><?php echo t('This is an internal block type.')?></p>
			<?php  } ?>
	
			<?php 
			$buttons[] = $ch->button(t("Refresh"), $this->url('/dashboard/install','refresh_block_type', $bt->getBlockTypeID()), "left");
			$u = new User();
			
			if ($u->isSuperUser()) {
			
				$removeBTConfirm = t('This will remove all instances of the %s block type. This cannot be undone. Are you sure?', $bt->getBlockTypeHandle());
				
				$buttons[] = $ch->button_js(t('Remove'), 'removeBlockType()', 'left');?>
	
				<script type="text/javascript">
				removeBlockType = function() {
					if (confirm('<?php echo $removeBTConfirm?>')) { 
						location.href = "<?php echo $this->url('/dashboard/install', 'uninstall_block_type', $bt->getBlockTypeID(), $valt->generate('uninstall'))?>";				
					}
				}
				</script>
	
			<?php  } else { ?>
				<?php  $buttons[] = $ch->button_js(t('Remove'), 'alert(\'' . t('Only the super user may remove block types.') . '\')', 'left', 'ccm-button-inactive');?>
			<?php  }
			
			print $ch->buttons($buttons); ?>
			
		</div>
				
	<?php  } else { ?>
		
		<!--[if IE 7]>
		<style type="text/css">
		td.ccm-addon-list-description {width: 161px !important}
		</style>
		<![endif]-->
		<div style="width: 720px">
		<div class="ccm-module" style="width: 350px; margin-bottom: 20px">
			
			<h1><span><?php echo t('Currently Installed')?></span></h1>
			<div class="ccm-dashboard-inner">
			<?php  if (count($pkgArray) > 0) { ?>
			<h2><?php echo t('Packages')?></h2>
			
				<?php 	foreach ($pkgArray as $pkg) { ?>
					<div class="ccm-addon-list">
					<table cellspacing="0" cellpadding="0">		
					<tr>
						<td class="ccm-installed-items-icon"><img src="<?php echo $ci->getPackageIconURL($pkg)?>" /></td>
						<td class="ccm-addon-list-description"><h3><?php echo $pkg->getPackageName()?> - <?php echo $pkg->getPackageVersion()?></a></h3><?php echo $pkg->getPackageDescription()?>

						</td>
						<td><?php echo $ch->button(t("Edit"), View::url('/dashboard/install', 'inspect_package', $pkg->getPackageID()), "right")?></td>					
					</tr>
					</table>
					</div>
				<?php  } ?>				
		
				<br/><br/>
	
			<?php  } ?>
			
			<?php  if (count($webBlockTypes) > 0) { ?>
				<h2><?php echo t('Custom Block Types')?></h2>
				<?php 	foreach ($webBlockTypes as $bt) { ?>
					<div class="ccm-addon-list">
					<table cellspacing="0" cellpadding="0">		
					<tr>
						<td class="ccm-installed-items-icon"><img src="<?php echo $ci->getBlockTypeIconURL($bt)?>" /></td>
						<td class="ccm-addon-list-description"><h3><?php echo $bt->getBlockTypeName()?></a></h3><?php echo $bt->getBlockTypeDescription()?></td>
						<td><?php echo $ch->button(t("Edit"), View::url('/dashboard/install', 'inspect_block_type', $bt->getBlockTypeID()), "right")?></td>					
					</tr>
					</table>
					</div>
				<?php  } ?>
				<br/><br/>
			<?php  } ?>
			
			<h2><?php echo t('Core Block Types')?></h2>
			<?php  
			if (count($coreBlockTypes) == 0) { ?>
				<p><?php echo t('No block types have been installed.')?></p>
			<?php  } else { ?>
			
				<?php 	foreach ($coreBlockTypes as $bt) { ?>
					<div class="ccm-addon-list">
					<table cellspacing="0" cellpadding="0">		
					<tr>
						<td class="ccm-installed-items-icon"><img src="<?php echo $ci->getBlockTypeIconURL($bt)?>" /></td>
						<td class="ccm-addon-list-description"><h3><?php echo $bt->getBlockTypeName()?></a></h3><?php echo $bt->getBlockTypeDescription()?></td>
						<td><?php echo $ch->button(t("Edit"), View::url('/dashboard/install', 'inspect_block_type', $bt->getBlockTypeID()), "right")?></td>					
					</tr>
					</table>
					</div>
				<?php  } ?>				
			<?php  } ?>
	
			</div>
				
		</div>
	
		<div class="ccm-module" style="width: 350px; margin-bottom: 20px">
				<?php  if ($updates > 0) { ?>
				<h1><span><?php echo t('Updates')?></span></h1>
				<div class="ccm-dashboard-inner">
					<?php  if ($updates == 1) { ?>
						<?php echo t('There is currently <strong>1</strong> update available.')?>
					<?php  } else { ?>
						<?php echo t('There are currently <strong>%s</strong> updates available.', $updates)?>
					<?php  } ?>
					<?php  print $ch->button(t('Update Addons'), $this->url('/dashboard/install/update'))?>
					
					<div class="ccm-spacer">&nbsp;</div>
				
				</div>
				
			
			<br/>
			<?php  } ?>
			

			<h1><span><?php echo t('New')?></span></h1>
			<div class="ccm-dashboard-inner">
			 
			<?php  if (ENABLE_MARKETPLACE_SUPPORT) { ?>
					
			<div class="ccm-addon-marketplace-account">
			<?php  
			Loader::library('marketplace');
			if ($mi->isConnected()) { ?>				
				<?php echo t('Your site is currently connected to the concrete5 community.')?><br/><br/>
				<?php  if (count($purchasedBlocks) == 0) { ?>
					<?php echo t('There appears to be nothing currently available to install from your <a href="%s" target="_blank">project page</a>.', $mi->getSitePageURL())?><br/><br/>
				<?php  } ?>
				<?php echo t('Browse more <a href="%s">add-ons</a> and <a href="%s">themes</a>, and check on your <a href="%s" target="_blank">project page</a>.', $this->url('/dashboard/install/', 'browse', 'addons'), $this->url('/dashboard/install', 'browse', 'themes'), $mi->getSitePageURL())?>
				<br/><br/>
				<a href="<?php echo $this->url('/dashboard/install', 'update')?>"><?php echo t("Check for updates &gt;")?></a>
			<?php 
			
			} else {
				Loader::element('dashboard/marketplace_connect_failed');
			}
			?>
				<div class="ccm-spacer">&nbsp;</div>
			</div>
			
			<?php  } ?>
			
		<?php  if (count($availableArray) == 0 && count($purchasedBlocks) == 0) { ?>
			
			<?php  if (!$mi->isConnected()) { ?>
				<?php echo t('Nothing currently available to install.')?>
			<?php  } ?>
			
		<?php  } else { ?>
	
			<div class="ccm-addon-list-wrapper">
			
			<?php  if (count($availableArray) > 0) { ?>
			<h2><?php echo t('Downloaded and Ready to Install')?></h2>
			<?php  } ?>
			<?php 	foreach ($availableArray as $obj) { ?>
				<div class="ccm-addon-list">
				<table cellspacing="0" cellpadding="0" border="0">
				<tr>
				<?php  if (get_class($obj) == "BlockType") { ?>
					<td><img src="<?php echo $ci->getBlockTypeIconURL($obj)?>" /></td>
					<td class="ccm-addon-list-description"><h3><?php echo $obj->getBlockTypeName()?></h3>
					<?php echo $obj->getBlockTypeDescription()?></td>
					<td><?php echo $ch->button(t("Install"), $this->url('/dashboard/install','install_block_type', $obj->getBlockTypeHandle()), "right");?></td>
				<?php  } else { ?>
					<td><img src="<?php echo $ci->getPackageIconURL($obj)?>" /></td>
					<td class="ccm-addon-list-description"><h3><?php echo $obj->getPackageName()?></h3>
					<?php echo $obj->getPackageDescription()?></td>
					<td><?php echo $ch->button(t("Install"), $this->url('/dashboard/install','install_package', $obj->getPackageHandle()), "right");?></td>
				<?php  } ?>
				</tr>
				</table>
				</div>
			<?php  } ?>
			
			<br/><Br/>
			<?php  if (count($purchasedBlocks) > 0) { ?>
			<h2><?php echo t('Ready to Download')?></h2>
			<?php  } ?>
	
			<?php  foreach ($purchasedBlocks as $pb) {
				$file = $pb->getRemoteFileURL();
				if (!empty($file)) {?>
				<div class="ccm-addon-list">
				<table cellspacing="0" cellpadding="0">
				<tr>
					<td><img src="<?php echo $pb->getRemoteIconURL()?>" /></td>
					<td class="ccm-addon-list-description"><h3><?php echo $pb->getName()?></h3>
					<?php echo $pb->getDescription()?>
					</td>
					<td width="120"><?php echo $ch->button(t("Download"), View::url('/dashboard/install', 'download', $pb->getMarketplaceItemID()), "right")?></td>
				</tr>
				</table>
				</div>
				<?php  } ?>
			<?php  } ?>
	
			</div>
	
			<?php  } ?>
	
			</div>
	
		</div>
	
		</div>
		</div>
		</div>
	
	<?php  } ?>
<?php  } ?>