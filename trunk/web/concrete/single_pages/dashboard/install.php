<?
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
    ccmAlert.notice("<?=$mtitle?>", "<?=$mmsg?>", 
		function() {
			location.href = '<?=$this->url('/dashboard/install')?>?ts=<?=time()?>';		
		});
}
function logoutSuccess() {
    ccmAlert.notice('Marketplace Logout', '<p>You have disconnected this site from the marketplace.</p>',
		function() {
			location.href = '<?=$this->url('/dashboard/install')?>?ts=<?=time()?>';		
		});
}
</script>

<? if (is_object($bt)) { ?>

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

	<div id="ccm-module-wrapper">
	<div style="width: 778px">

	<div class="ccm-module" style="width: 320px; margin-bottom: 0px">

		<h1><span><?=t('Currently Installed')?></span></h1>
		<div class="ccm-dashboard-inner">
		
		<? 
		if (count($installedArray) == 0) { ?>
			<p><?=t('No block types have been installed.')?></p>
		<? } else { ?>
		
			<div style="margin:0px; padding:0px; height:auto">	
	
			<?	foreach ($installedArray as $bt) { ?>
				<div class="ccm-block-type" style="border-bottom: none">
					<a class="ccm-block-type-inner" style="background-image: url(<?=$ci->getBlockTypeIconURL($bt)?>)" href="<?=$this->url('/dashboard/install', 'inspect_block_type', $bt->getBlockTypeID())?>" title="<?=$bt->getBlockTypeDescription()?>"><?=$bt->getBlockTypeName()?></a>
				</div>
			<? } ?>

			</div>
				
		<? } ?>

		<?  /* if (count($pkgArray) == 0) { ?>
			<p><?=t('No packages have been installed.')?></p>
		<? } else { ?>
		
			<div style="margin:0px; padding:0px; height:auto">	
	
			<?	foreach ($pkgArray as $pkg) { ?>
				<div class="ccm-block-type" style="border-bottom: none">
					<div class="ccm-block-type-inner" style="background-image: url(<?=$ci->getPackageIconURL($pkg)?>)"><?=$pkg->getPackageName()?></a>
				</div>
			<? } ?>

			</div>
				
		<? }*/  ?>

		</div>
			
	</div>

	<div class="ccm-module" style="width: 380px; margin-bottom: 0px">

		<h1><span><?=t('New')?></span></h1>
		<div class="ccm-dashboard-inner">
		 
		<? if (ENABLE_MARKETPLACE_SUPPORT) { ?>
		<p>		
		<?=t('You can safely and easily extend your website without touching a line of code. Connect to the <a href="%s" target="_blank">concrete5.org marketplace</a>, and you can automatically install your themes and add-ons right here!', MARKETPLACE_URL_LANDING)?>
		</p>
				
		<hr />		

		<? if (!UserInfo::isRemotelyLoggedIn()) { ?> 
			<p><a href="#" onclick="ccmPopupLogin.show('', loginSuccess, '', 1)">Sign in or create an account.</a></p>			
		<? } else { ?> 
			<p><?=t('You have connected this website to the concrete5 marketplace as  ');?>
          	  <a href="<?=CONCRETE5_ORG_URL ?>/profile/-/<?=UserInfo::getRemoteAuthUserId() ?>/" target="_blank" ><?=UserInfo::getRemoteAuthUserName() ?></a>
			  <?=t('(Not your account? <a href="#" onclick="ccm_support.signOut(logoutSuccess)">Sign Out</a>)')?></p>
		<? } ?>
		<hr />
		
		<? } ?>
		
	<? if (count($availableArray) == 0 && count($purchasedBlocks) == 0) { ?>

		<?=t('Nothing currently available to install.')?>
	
	<? } else { ?>

		<div class="ccm-addon-list-wrapper">
		<? foreach ($purchasedBlocks as $pb) {
			if (in_array($pb->getBlockTypeHandle(), $skipHandles)) {
				continue;
			}
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
		</div>

		<? } ?>

		</div>

	</div>

	</div>
	</div>

<? } ?>
