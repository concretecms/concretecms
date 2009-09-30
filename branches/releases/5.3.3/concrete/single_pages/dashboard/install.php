<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$valt = Loader::helper('validation/token');
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');

$pkgArray = Package::getInstalledList();

if ($this->controller->getTask() == 'update') { 

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
	<?php  if (!UserInfo::isRemotelyLoggedIn()) { ?> 
	<div class="ccm-addon-marketplace-account">
	
		<?php echo t('You must sign in to the concrete5.org marketplace to check for updates to your add-ons.')?><br/><br/>
		<a href="#" onclick="ccmPopupLogin.show('', loginSuccess, '', 1)">Sign in or create an account.</a>
		
	</div>
	
	<?php  } else { ?> 
	<div class="ccm-addon-marketplace-account">
		<?php echo t('You have connected this website to the concrete5 marketplace as  ');?>
		  <a href="<?php echo CONCRETE5_ORG_URL ?>/profile/-/<?php echo UserInfo::getRemoteAuthUserId() ?>/" target="_blank" ><?php echo UserInfo::getRemoteAuthUserName() ?></a>
		  <?php echo t('(Not your account? <a href="#" onclick="ccm_support.signOut(logoutSuccess)">Sign Out</a>)')?>
	</div>

	

	<h2><?php echo t('The Following Updates are Available')?></h2>
	
	<?php 
	$mh = Loader::helper('concrete/marketplace/blocks');
	$purchased = $mh->getPurchasesList(false);
	$i = 0;
	
	foreach ($pkgArray as $pkg) { 
		$rpkg = $purchased[$pkg->getPackageHandle()];
		if (!is_object($rpkg)) {
			continue;
		}
		if (version_compare($rpkg->getVersion(), $pkg->getPackageVersion(), '>')) { 
			$i++;
			
			?>
			<div class="ccm-addon-list">
			<table cellspacing="0" cellpadding="0" border="0" style="width: auto !important">		
			<tr>
				<td valign="top" class="ccm-installed-items-icon"><img src="<?php echo $ci->getPackageIconURL($pkg)?>" /></td>
				<td valign="top" class="ccm-addon-list-description" style="width: 400px"><h3><?php echo $pkg->getPackageName()?></a></h3><?php echo $pkg->getPackageDescription()?>
				<br/><br/>
				<strong><?php echo t('Current Version: %s', $pkg->getPackageVersion())?></strong><br/>
				<strong><?php echo t('New Version: %s', $rpkg->getVersion())?></strong><br/>
				<a target="_blank" href="<?php echo $rpkg->getRemoteURL()?>"><?php echo t('More Information')?></a>
				</td>
				<td valign="top"><?php echo $ch->button(t("Download and Install"), View::url('/dashboard/install', 'remote_upgrade', $rpkg->getRemoteCollectionID(), $pkg->getPackageHandle()), "right")?></td>					
			</tr>
			</table>
			</div>
		<?php  } ?>			
	<?php  }
		
		if ($i == 0) { ?>
			
			<p><?php echo t('There are no updates for your add-ons currently available from the marketplace.')?></p>
			
			
		<?php  } ?>
	
	<?php  } ?>
</div>

<?php  } ?>

<?php  
} else { 

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
	ksort($availableArray);
	
	/* Load featured add-ons from the marketplace.
	 */
	Loader::model('collection_attributes');
	$db = Loader::db();
	
	if(ENABLE_MARKETPLACE_SUPPORT){
		$blocksHelper = Loader::helper('concrete/marketplace/blocks');
		$purchasedBlocksSource = $blocksHelper->getPurchasesList();
	}else{
		$purchasedBlocksSource = array();
	}
	
	// now we iterate through the purchased items (NOT BLOCKS, THESE CAN INCLUDE THEMES) list and removed ones already downloaded
	// This really should be made into a more generic object since it's not block types anymore.
	
	$skipHandles = array();
	foreach($availableArray as $ava) {
		foreach($purchasedBlocksSource as $pi) {
			if ($pi->getBlockTypeHandle() == $ava->getPackageHandle()) {
				$skipHandles[] = $ava->getPackageHandle();
			}
		}
	}
	
	$purchasedBlocks = array();
	foreach($purchasedBlocksSource as $pb) {
		if (!in_array($pb->getBlockTypeHandle(), $skipHandles)) {
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
			foreach($items as $_b) {
				if ($_b instanceof BlockType) {
					$blocks[] = $_b;
				}
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
			<?php  }
			
			$u = new User();
			if ($u->isSuperUser()) {
			
				$removeBTConfirm = t('This will remove all elements associated with the %s package. This cannot be undone. Are you sure?', $pkg->getPackageHandle());
				
				$buttons[] = $ch->button_js(t('Uninstall Package'), 'removePackage()', 'left');?>
	
				<script type="text/javascript">
				removePackage = function() {
					if (confirm('<?php echo $removeBTConfirm?>')) { 
						location.href = "<?php echo $this->url('/dashboard/install', 'uninstall_package', $pkg->getPackageID(), $valt->generate('uninstall'))?>";				
					}
				}
				</script>
	
			<?php  } else { ?>
				<?php  $buttons[] = $ch->button_js(t('Remove'), 'alert(\'' . t('Only the super user may remove packages.') . '\')', 'left', 'ccm-button-inactive');?>
			<?php  }
	
			print $ch->buttons($buttons); ?>
			
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
	
			<h1><span><?php echo t('New')?></span></h1>
			<div class="ccm-dashboard-inner">
			 
			<?php  if (ENABLE_MARKETPLACE_SUPPORT) { ?>
			<p>		
			<?php echo t('You can safely and easily extend your website without touching a line of code. Connect to the <a href="%s" target="_blank">concrete5.org marketplace</a>, and you can automatically install your themes and add-ons right here!', MARKETPLACE_URL_LANDING)?>
			</p>
					
			<div class="ccm-addon-marketplace-account">
	
			<?php  if (!UserInfo::isRemotelyLoggedIn()) { ?> 
				<a href="#" onclick="ccmPopupLogin.show('', loginSuccess, '', 1)">Sign in or create an account.</a>
			<?php  } else { ?> 
				<?php echo t('You have connected this website to the concrete5 marketplace as  ');?>
				  <a href="<?php echo CONCRETE5_ORG_URL ?>/profile/-/<?php echo UserInfo::getRemoteAuthUserId() ?>/" target="_blank" ><?php echo UserInfo::getRemoteAuthUserName() ?></a>
				  <?php echo t('(Not your account? <a href="#" onclick="ccm_support.signOut(logoutSuccess)">Sign Out</a>)')?>
			<?php  } ?>
			
			</div>
			
			<?php  } ?>
			
		<?php  if (count($availableArray) == 0 && count($purchasedBlocks) == 0) { ?>
	
			<?php echo t('Nothing currently available to install.')?>
		
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
					<td class="ccm-addon-list-description"><h3><?php echo $pb->btName?></h3>
					<?php echo $pb->btDescription?>
					</td>
					<td width="120"><?php echo $ch->button(t("Download"), View::url('/dashboard/install', 'remote_purchase', $pb->getRemoteCollectionID()), "right")?></td>
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
<?php  }


$mtitle = t('Marketplace Login');
$mlogouttitle = t('Marketplace Logout');
$mmsg = t("You've successfully connected this website to your concrete5 Marketplace account. Featured items will be visible to you while using this site. You can browse the complete marketplace at <a href='%s' target='_blank'>concrete5.org/marketplace</a>", 'http://www.concrete5.org/marketplace/');
$mlogoutmsg = t("You have disconnected this site from the marketplace.");

?>

<script type="text/javascript">
function loginSuccess() {
	jQuery.fn.dialog.closeTop();
	ccmAlert.notice("<?php echo $mtitle?>", "<?php echo $mmsg?>", 
		function() {
			location.href = '<?php echo $this->url($thisURL)?>?ts=<?php echo time()?>';		
		});
}
function logoutSuccess() {
	ccmAlert.notice("<?php echo $mlogouttitle?>", "<?php echo $mlogoutmsg?>", 
		function() {
			location.href = '<?php echo $this->url($thisURL)?>?ts=<?php echo time()?>';		
		});
}
</script>
