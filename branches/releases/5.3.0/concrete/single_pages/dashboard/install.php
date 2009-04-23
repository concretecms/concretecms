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

?>

<script type="text/javascript">
function loginSuccess() {
    jQuery.fn.dialog.closeTop();
    ccmAlert.notice('Marketplace Login', '<p>You have successfully logged into the concrete5 marketplace.</p>',
		function() {str=unescape(window.location.pathname); window.location.href = str.replace(/\/-\/.*/, '');});
}
function logoutSuccess() {
    ccmAlert.notice('Marketplace Logout', '<p>You are now logged out of concrete5 marketplace.</p>',
		function() {str=unescape(window.location.pathname); window.location.href = str.replace(/\/-\/.*/, '');});
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
		if ($bt->canUnInstall()) {
			$buttons[] = $ch->button(t("Remove"), $this->url('/dashboard/install', 'uninstall_block_type', $bt->getBlockTypeID(), $valt->generate('uninstall')), "left");
		}
	
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

		<?php  if (!UserInfo::isRemotelyLoggedIn()) { ?>
			<p><?php echo t('You are not currently signed in to the marketplace.')?></p>
			<p><a href="#" onclick="ccmPopupLogin.show('', loginSuccess, '', 1)">Click here to sign in or create an account.</a></p>
		<?php  } else { ?>
			<p><?php echo t('You are currently signed in to the marketplace as ');?>
          	  <a href="<?php echo CONCRETE5_ORG_URL ?>/profile/-/<?php echo UserInfo::getRemoteAuthUserId() ?>/" ><?php echo UserInfo::getRemoteAuthUserName() ?></a>
			  <?php echo t('(Not your account? <a href="#" onclick="ccm_support.signOut(logoutSuccess)">Sign Out</a>)')?></p>
		<?php  } ?>
		<hr />
		
		<?php  } ?>
		
	<?php  if (count($availableArray) == 0 && count($purchasedBlocks) == 0) { ?>

		<?php echo t('Nothing is available to install.')?>
	
	<?php  } else { ?>

		<div style="margin:0px; padding:0px;  height:auto">
		<?php  foreach ($purchasedBlocks as $pb) {
			$file = $pb->getRemoteFileURL();
			if (!empty($file)) {?>
			<div class="ccm-block-type">
			<table width="100%">
			<tr>
				<td rowspan="2"><img src="<?php echo $pb->getRemoteIconURL()?>" style="width:90px;height:90px; margin-right: 8px"></td>
				<td><p class="ccm-block-type-inner-nobkgd"><?php echo $pb->btName?></p></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="color: #aaa; padding: 2px 0 6px"><?php echo $pb->btDescription?></td>
				<td style="vertical-align: bottom"><?php echo $ch->button(t("Download"), View::url('/dashboard/install', 'remote_purchase', $pb->getRemoteCollectionID()), "right")?></td>
			</tr>
			</table>
			</div>
			<?php  } ?>
		<?php  } ?>
		</div>

		<div style="margin:0px; padding:0px;  height:auto">
		<?php 	foreach ($availableArray as $obj) { ?>
			<div class="ccm-block-type">
			<table width="100%">
			<tr>
			<?php  if (get_class($obj) == "BlockType") { ?>
				<td rowspan="2"><img src="<?php echo $ci->getBlockTypeIconURL($obj)?>" style="width:90px;height:90px"></td>
				<td><p class="ccm-block-type-inner-nobkgd"><?php echo $obj->getBlockTypeName()?></p></td>
			<?php  } else { ?>
				<td rowspan="2"><img src="<?php echo $ci->getPackageIconURL($obj)?>" style="width:90px;height:90px"></td>
				<td><p class="ccm-block-type-inner-nobkgd"><?php echo $obj->getPackageName()?></p></td>
			<?php  } ?>
				<td>&nbsp;</td>
			</tr>
			<tr>
			<?php  if (get_class($obj) == "BlockType") { ?>
				<td style="color: #aaa; padding: 2px 0 6px"><?php echo $obj->getBlockTypeDescription()?></td>
				<td style="vertical-align: bottom"><?php echo $ch->button(t("Install"), $this->url('/dashboard/install','install_block_type', $obj->getBlockTypeHandle()), "right");?></td>
			<?php  } else { ?>
				<td style="color: #aaa; padding: 2px 0 6px"><?php echo $obj->getPackageDescription()?></td>
				<td style="vertical-align: bottom"><?php echo $ch->button(t("Install"), $this->url('/dashboard/install','install_package', $obj->getPackageHandle()), "right");?></td>
			<?php  } ?>
			</tr>
			</table>
			</div>
		<?php  } ?>
		</div>

		<?php  } ?>

		<?php  if (ENABLE_MARKETPLACE_SUPPORT) { ?>		
		<hr />
		<p><strong><?php echo t('You can extend your site with new addons and themes from the <a href="%s" target="_blank">concrete5 marketplace</a>.', MARKETPLACE_URL_LANDING);?></strong></p>
		
		<?php  } ?>
		
		</div>

	</div>

	</div>
	</div>

<?php  } ?>
