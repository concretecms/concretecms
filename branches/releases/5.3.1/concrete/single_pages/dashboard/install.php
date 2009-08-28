<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$valt = Loader::helper('validation/token');

/* Load installed and available blocks and packages.
 */
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');

$btArray = BlockTypeList::getInstalledList();
$btAvailableArray = BlockTypeList::getAvailableList();
$pkgArray = Package::getInstalledList();
$pkgAvailableArray = Package::getAvailablePackages();
$installedArray = $btArray;
$availableArray = array_merge($btAvailableArray, $pkgAvailableArray);
ksort($availableArray);

/* Load featured add-ons from the marketplace.
 */
Loader::model('collection_attributes');
$db = Loader::db();

if(ENABLE_MARKETPLACE_SUPPORT){
	$blocksHelper = Loader::helper('concrete/marketplace/blocks');
	$purchasedBlocks = $blocksHelper->getPurchasesList();
}else{
    $purchasedBlocks = array();
}

// now we iterate through the purchased items (NOT BLOCKS, THESE CAN INCLUDE THEMES) list and removed ones already downloaded
// This really should be made into a more generic object since it's not block types anymore.

$skipHandles = array();
foreach($availableArray as $ava) {
	foreach($purchasedBlocks as $pi) {
		if ($pi->getBlockTypeHandle() == $ava->getPackageHandle()) {
			$skipHandles[] = $ava->getPackageHandle();
		}
	}
}

$mtitle = t('Marketplace Login');
$mmsg = t("You've successfully connected this website to your concrete5 Marketplace account. Featured items will be visible to you while using this site. You can browse the complete marketplace at <a href='%s' target='_blank'>concrete5.org/marketplace</a>", 'http://www.concrete5.org/marketplace/');
?>
<script type="text/javascript">
function loginSuccess() {
    jQuery.fn.dialog.closeTop();
    ccmAlert.notice("<?php echo $mtitle?>", "<?php echo $mmsg?>", 
		function() {
			location.href = '<?php echo $this->url('/dashboard/install')?>?ts=<?php echo time()?>';		
		});
}
function logoutSuccess() {
    ccmAlert.notice('Marketplace Logout', '<p>You have disconnected this site from the marketplace.</p>',
		function() {
			location.href = '<?php echo $this->url('/dashboard/install')?>?ts=<?php echo time()?>';		
		});
}
</script>

<?php  if (is_object($bt)) { ?>

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

	<div id="ccm-module-wrapper">
	<div style="width: 778px">

	<div class="ccm-module" style="width: 320px; margin-bottom: 0px">

		<h1><span><?php echo t('Currently Installed')?></span></h1>
		<div class="ccm-dashboard-inner">
		
		<?php  
		if (count($installedArray) == 0) { ?>
			<p><?php echo t('No block types have been installed.')?></p>
		<?php  } else { ?>
		
			<div style="margin:0px; padding:0px; height:auto">	
	
			<?php 	foreach ($installedArray as $bt) { ?>
				<div class="ccm-block-type" style="border-bottom: none">
					<a class="ccm-block-type-inner" style="background-image: url(<?php echo $ci->getBlockTypeIconURL($bt)?>)" href="<?php echo $this->url('/dashboard/install', 'inspect_block_type', $bt->getBlockTypeID())?>" title="<?php echo $bt->getBlockTypeDescription()?>"><?php echo $bt->getBlockTypeName()?></a>
				</div>
			<?php  } ?>

			</div>
				
		<?php  } ?>

		<?php   /* if (count($pkgArray) == 0) { ?>
			<p><?php echo t('No packages have been installed.')?></p>
		<?php  } else { ?>
		
			<div style="margin:0px; padding:0px; height:auto">	
	
			<?php 	foreach ($pkgArray as $pkg) { ?>
				<div class="ccm-block-type" style="border-bottom: none">
					<div class="ccm-block-type-inner" style="background-image: url(<?php echo $ci->getPackageIconURL($pkg)?>)"><?php echo $pkg->getPackageName()?></a>
				</div>
			<?php  } ?>

			</div>
				
		<?php  }*/  ?>

		</div>
			
	</div>

	<div class="ccm-module" style="width: 380px; margin-bottom: 0px">

		<h1><span><?php echo t('New')?></span></h1>
		<div class="ccm-dashboard-inner">
		 
		<?php  if (ENABLE_MARKETPLACE_SUPPORT) { ?>
		<p>		
		<?php echo t('You can safely and easily extend your website without touching a line of code. Connect to the <a href="%s" target="_blank">concrete5.org marketplace</a>, and you can automatically install your themes and add-ons right here!', MARKETPLACE_URL_LANDING)?>
		</p>
				
		<hr />		

		<?php  if (!UserInfo::isRemotelyLoggedIn()) { ?> 
			<p><a href="#" onclick="ccmPopupLogin.show('', loginSuccess, '', 1)">Sign in or create an account.</a></p>			
		<?php  } else { ?> 
			<p><?php echo t('You have connected this website to the concrete5 marketplace as  ');?>
          	  <a href="<?php echo CONCRETE5_ORG_URL ?>/profile/-/<?php echo UserInfo::getRemoteAuthUserId() ?>/" target="_blank" ><?php echo UserInfo::getRemoteAuthUserName() ?></a>
			  <?php echo t('(Not your account? <a href="#" onclick="ccm_support.signOut(logoutSuccess)">Sign Out</a>)')?></p>
		<?php  } ?>
		<hr />
		
		<?php  } ?>
		
	<?php  if (count($availableArray) == 0 && count($purchasedBlocks) == 0) { ?>

		<?php echo t('Nothing currently available to install.')?>
	
	<?php  } else { ?>

		<div class="ccm-addon-list-wrapper">
		<?php  foreach ($purchasedBlocks as $pb) {
			if (in_array($pb->getBlockTypeHandle(), $skipHandles)) {
				continue;
			}
			$file = $pb->getRemoteFileURL();
			if (!empty($file)) {?>
			<div class="ccm-addon-list">
			<table cellspacing="0" cellpadding="0">
			<tr>
				<td><img src="<?php echo $pb->getRemoteIconURL()?>" /></td>
				<td class="ccm-addon-list-description"><h3><?php echo $pb->btName?></h3>
				<?php echo $pb->btDescription?>
				</td>
				<td><?php echo $ch->button(t("Download"), View::url('/dashboard/install', 'remote_purchase', $pb->getRemoteCollectionID()), "right")?></td>
			</tr>
			</table>
			</div>
			<?php  } ?>
		<?php  } ?>

		<?php 	foreach ($availableArray as $obj) { ?>
			<div class="ccm-addon-list">
			<table cellspacing="0" cellpadding="0">
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
		</div>

		<?php  } ?>

		</div>

	</div>

	</div>
	</div>

<?php  } ?>
