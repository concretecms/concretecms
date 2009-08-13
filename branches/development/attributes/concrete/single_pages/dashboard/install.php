<?
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
	
	<h1><span><?=t('Downloaded and Ready to Install')?></span></h1>
	
	
	<div class="ccm-dashboard-inner">
	<? foreach ($pkgAvailableArray as $pkg) {  ?>
		<div class="ccm-addon-list">
			<table cellspacing="0" cellpadding="0">		
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
	<? if (!UserInfo::isRemotelyLoggedIn()) { ?> 
	<div class="ccm-addon-marketplace-account">
	
		<?=t('You must sign in to the concrete5.org marketplace to check for updates to your add-ons.')?><br/><br/>
		<a href="#" onclick="ccmPopupLogin.show('', loginSuccess, '', 1)">Sign in or create an account.</a>
		
	</div>
	
	<? } else { ?> 
	<div class="ccm-addon-marketplace-account">
		<?=t('You have connected this website to the concrete5 marketplace as  ');?>
		  <a href="<?=CONCRETE5_ORG_URL ?>/profile/-/<?=UserInfo::getRemoteAuthUserId() ?>/" target="_blank" ><?=UserInfo::getRemoteAuthUserName() ?></a>
		  <?=t('(Not your account? <a href="#" onclick="ccm_support.signOut(logoutSuccess)">Sign Out</a>)')?>
	</div>

	

	<h2><?=t('The Following Updates are Available')?></h2>
	
	<?
	$mh = Loader::helper('concrete/marketplace/blocks');
	$purchased = $mh->getPurchasesList(false);
	$i = 0;
	
	foreach ($pkgArray as $pkg) { 
		$rpkg = $purchased[$pkg->getPackageHandle()];
		if (version_compare($rpkg->getVersion(), $pkg->getPackageVersion(), '>')) { 
			$i++;
			
			?>
			<div class="ccm-addon-list">
			<table cellspacing="0" cellpadding="0">		
			<tr>
				<td class="ccm-installed-items-icon"><img src="<?=$ci->getPackageIconURL($pkg)?>" /></td>
				<td class="ccm-addon-list-description"><h3><?=$pkg->getPackageName()?></a></h3><?=$pkg->getPackageDescription()?>
				<br/><br/>
				<strong><?=t('Current Version: %s', $pkg->getPackageVersion())?></strong><br/>
				<strong><?=t('New Version: %s', $rpkg->getVersion())?></strong><br/>
				<a target="_blank" href="<?=$rpkg->getRemoteURL()?>"><?=t('More Information')?></a>
				</td>
				<td><?=$ch->button(t("Download and Install"), View::url('/dashboard/install', 'remote_upgrade', $rpkg->getRemoteCollectionID(), $pkg->getPackageHandle()), "right")?></td>					
			</tr>
			</table>
			</div>
		<? } ?>			
	<? }
		
		if ($i == 0) { ?>
			
			<p><?=t('There are no updates for your add-ons currently available from the marketplace.')?></p>
			
			
		<? } ?>
	
	<? } ?>
</div>

<? } ?>

<? 
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
	
		<h1><span><?=$pkg->getPackageName()?></span></h1>
		<div class="ccm-dashboard-inner">
			<img src="<?=$ci->getPackageIconURL($pkg)?>" style="float: right" />
			<div><a href="<?=$this->url('/dashboard/install')?>">&lt; <?=t('Return to Add Functionality')?></a></div><br/>
				
			<h2><?=t('Description')?></h2>
			<p><?=$pkg->getPackageDescription()?></p>
		
			<?
			
			$items = $pkg->getPackageItems();
			$blocks = array();
			foreach($items as $_b) {
				if ($_b instanceof BlockType) {
					$blocks[] = $_b;
				}
			}
			
			if (count($blocks) > 0) { ?>
				<h2><?=t("Block Types")?></h2>
				<? foreach($blocks as $bt) { ?>
	
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
			<? }
			
			$u = new User();
			if ($u->isSuperUser()) {
			
				$removeBTConfirm = t('This will remove all elements associated with the %s package. This cannot be undone. Are you sure?', $pkg->getPackageHandle());
				
				$buttons[] = $ch->button_js(t('Uninstall Package'), 'removePackage()', 'left');?>
	
				<script type="text/javascript">
				removePackage = function() {
					if (confirm('<?=$removeBTConfirm?>')) { 
						location.href = "<?=$this->url('/dashboard/install', 'uninstall_package', $pkg->getPackageID(), $valt->generate('uninstall'))?>";				
					}
				}
				</script>
	
			<? } else { ?>
				<? $buttons[] = $ch->button_js(t('Remove'), 'alert(\'' . t('Only the super user may remove packages.') . '\')', 'left', 'ccm-button-inactive');?>
			<? }
	
			print $ch->buttons($buttons); ?>
			
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
	
		<div class="ccm-module" style="width: 350px; margin-bottom: 20px">
	
			<h1><span><?=t('New')?></span></h1>
			<div class="ccm-dashboard-inner">
			 
			<? if (ENABLE_MARKETPLACE_SUPPORT) { ?>
			<p>		
			<?=t('You can safely and easily extend your website without touching a line of code. Connect to the <a href="%s" target="_blank">concrete5.org marketplace</a>, and you can automatically install your themes and add-ons right here!', MARKETPLACE_URL_LANDING)?>
			</p>
					
			<div class="ccm-addon-marketplace-account">
	
			<? if (!UserInfo::isRemotelyLoggedIn()) { ?> 
				<a href="#" onclick="ccmPopupLogin.show('', loginSuccess, '', 1)">Sign in or create an account.</a>
			<? } else { ?> 
				<?=t('You have connected this website to the concrete5 marketplace as  ');?>
				  <a href="<?=CONCRETE5_ORG_URL ?>/profile/-/<?=UserInfo::getRemoteAuthUserId() ?>/" target="_blank" ><?=UserInfo::getRemoteAuthUserName() ?></a>
				  <?=t('(Not your account? <a href="#" onclick="ccm_support.signOut(logoutSuccess)">Sign Out</a>)')?>
			<? } ?>
			
			</div>
			
			<? } ?>
			
		<? if (count($availableArray) == 0 && count($purchasedBlocks) == 0) { ?>
	
			<?=t('Nothing currently available to install.')?>
		
		<? } else { ?>
	
			<div class="ccm-addon-list-wrapper">
			
			<? if (count($availableArray) > 0) { ?>
			<h2><?=t('Downloaded and Ready to Install')?></h2>
			<? } ?>
			<?	foreach ($availableArray as $obj) { ?>
				<div class="ccm-addon-list">
				<table cellspacing="0" cellpadding="0">
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
					<td class="ccm-addon-list-description"><h3><?=$pb->btName?></h3>
					<?=$pb->btDescription?>
					</td>
					<td><?=$ch->button(t("Download"), View::url('/dashboard/install', 'remote_purchase', $pb->getRemoteCollectionID()), "right")?></td>
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
		</div>
	
	<? } ?>
<? }


$mtitle = t('Marketplace Login');
$mlogouttitle = t('Marketplace Logout');
$mmsg = t("You've successfully connected this website to your concrete5 Marketplace account. Featured items will be visible to you while using this site. You can browse the complete marketplace at <a href='%s' target='_blank'>concrete5.org/marketplace</a>", 'http://www.concrete5.org/marketplace/');
$mlogoutmsg = t("You have disconnected this site from the marketplace.");

?>

<script type="text/javascript">
function loginSuccess() {
	jQuery.fn.dialog.closeTop();
	ccmAlert.notice("<?=$mtitle?>", "<?=$mmsg?>", 
		function() {
			location.href = '<?=$this->url($thisURL)?>?ts=<?=time()?>';		
		});
}
function logoutSuccess() {
	ccmAlert.notice("<?=$mlogouttitle?>", "<?=$mlogoutmsg?>", 
		function() {
			location.href = '<?=$this->url($thisURL)?>?ts=<?=time()?>';		
		});
}
</script>
