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
		if ($bt->canUnInstall()) {
			$buttons[] = $ch->button(t("Remove"), $this->url('/dashboard/install', 'uninstall_block_type', $bt->getBlockTypeID(), $valt->generate('uninstall')), "left");
		}
	
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

		<? if (!UserInfo::isRemotelyLoggedIn()) { ?>
			<p><?=t('You are not currently signed in to the marketplace.')?></p>
			<p><a href="#" onclick="ccmPopupLogin.show('', loginSuccess, '', 1)">Click here to sign in or create an account.</a></p>
		<? } else { ?>
			<p><?=t('You are currently signed in to the marketplace as ');?>
          	  <a href="<?=CONCRETE5_ORG_URL ?>/profile/-/<?=UserInfo::getRemoteAuthUserId() ?>/" ><?=UserInfo::getRemoteAuthUserName() ?></a>
			  <?=t('(Not your account? <a href="#" onclick="ccm_support.signOut(logoutSuccess)">Sign Out</a>)')?></p>
		<? } ?>
		<hr />

	<? if (count($availableArray) == 0 && count($purchasedBlocks) == 0) { ?>

		<?=t('Nothing is available to install.')?>
	
	<? } else { ?>

		<div style="margin:0px; padding:0px;  height:auto">
		<? foreach ($purchasedBlocks as $pb) {
			$style = $pb->getRemoteIconURL() ? 'style="background-image: url('.$pb->getRemoteIconURL().')"' : '';
			$file = $pb->getRemoteFileURL();
			$button = (!empty($file)) ? $ch->button(t("Download"), View::url('/dashboard/install', 'remote_purchase', $pb->getRemoteCollectionID()), "right")
			                          : $ch->button(t("Details"), $pb->getRemoteURL(), "right"); ?>
			<div class="ccm-block-type">
			<table width="100%">
			<? if (!empty($pb->btDescription)) {?>
				<tr>
					<td colspan="2"><p class="ccm-block-type-inner" <?=$style?>><?=$pb->btName?></p></td>
				</tr>
				<tr>
					<td style="color: #aaa; padding: 2px 0 6px"><?=$pb->btDescription?></td>
					<td style="vertical-align: bottom"><?=$button?></td>
				</tr>
			<? } else {?>
				<tr>
					<td><p class="ccm-block-type-inner" <?=$style?>><?=$pb->btName?></p></td>
					<td style="vertical-align: bottom"><?=$button?></td>
				</tr>
			<? } ?>
			</table>
			</div>
		<? } ?>
		</div>

		<div style="margin:0px; padding:0px;  height:auto">
		<?	foreach ($availableArray as $obj) { ?>
			<div class="ccm-block-type">
			<table width="100%">
			<tr>
			<td colspan="2">
			<? if (get_class($obj) == "BlockType") { ?>
				<p class="ccm-block-type-inner" style="background-image: url(<?=$ci->getBlockTypeIconURL($obj)?>)"><?=$obj->getBlockTypeName()?></p>
			<? } else { ?>
				<p class="ccm-block-type-inner" style="background-image: url(<?=$ci->getPackageIconURL($obj)?>)"><?=$obj->getPackageName()?></p>
			<? } ?>
			</td>
			</tr>
			<tr>
			<? if (get_class($obj) == "BlockType") { ?>
				<td style="color: #aaa; padding: 2px 0 6px"><?=$obj->getBlockTypeDescription()?></td>
				<td style="vertical-align: bottom"><?=$ch->button(t("Install"), $this->url('/dashboard/install','install_block_type', $obj->getBlockTypeHandle()), "right");?></td>
			<? } else { ?>
				<td style="color: #aaa; padding: 2px 0 6px"><?=$obj->getPackageDescription()?></td>
				<td style="vertical-align: bottom"><?=$ch->button(t("Install"), $this->url('/dashboard/install','install_package', $obj->getPackageHandle()), "right");?></td>
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
