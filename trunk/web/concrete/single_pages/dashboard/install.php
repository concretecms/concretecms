<?
defined('C5_EXECUTE') or die(_("Access Denied."));

$valt = Loader::helper('validation/token');

if (isset($_POST['task'])) {
	if ($_POST['task'] == 'install_blocktype') { 
		if (isset($_POST['btID']) && $_POST['btID'] > 0) {
			if ($_POST['pkgID']) {
				$pkg = Package::getByID($_POST['pkgID']);
				$resp = BlockType::installBlockTypeFromPackage($_POST['btHandle'], $pkg, $_POST['btID']);
			} else {
				$resp = BlockType::installBlockType($_POST['btHandle'], $_POST['btID']);
			}
		} else {
			$resp = BlockType::installBlockType($_POST['btHandle']);
		}
	
		if ($resp != '') {
			$error[] = $resp;
		} else {
			$this->controller->redirect('/dashboard/install?bt_installed=1');
		}
		
	}
}

if ($_REQUEST['bt_installed']) {
	$message = t('Add-On Installed');
}

/* Load installed and available blocks, packages, and themes.
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
$themesArray = PageTheme::getAvailableThemes();

/* Load featured add-ons and themes from the marketplace.
 */
Loader::model('collection_attributes');
$db = Loader::db();

$isFeaturedKeyId = CollectionAttributeKey::getByHandle('is_featured_remotely');

if(ENABLE_MARKETPLACE_SUPPORT){
	$blocksHelper = Loader::helper('concrete/marketplace/blocks');
	$themesHelper = Loader::helper('concrete/marketplace/themes');

	$featuredBlocks = $blocksHelper->getPreviewableList();
	$featuredThemes = $themesHelper->getPreviewableList();
}else{
    $featuredBlocks = $featuredThemes = array();
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

	<style>
	.ccm-module form{ width:auto; height:auto; padding:0px; padding-bottom:10px; display:block; }
	.ccm-module form div.ccm-dashboard-inner{ margin-bottom:0px !important; }
	</style>

	<div id="ccm-module-wrapper">
	<div style="width: 778px">
	<div style="float: left; width: 520px">

	<div class="ccm-module" style="width: 250px; margin-bottom: 0px">

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

		<? 
		/*
		if (count($pkgArray) == 0) { ?>
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

	<div class="ccm-module" style="width: 250px; margin-bottom: 0px">

		<h1><span><?=t('New')?></span></h1>
		<div class="ccm-dashboard-inner">

		<? if (count($availableArray) == 0) { ?>

		<?=t('Nothing is available to install.')?>
	
		<? } else { ?>

		<div style="margin:0px; padding:0px;  height:auto" >	
		<?	foreach ($availableArray as $obj) { ?>
			<div class="ccm-block-type">
			<table>
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

	<div class="ccm-module" style="width: 250px; margin-top: 10px; margin-bottom: 0px">

		<h1><span><?=t('Available Themes')?></span></h1>
		<div class="ccm-dashboard-inner">

		<? if (count($themesArray) == 0) { ?>
			<p><?=t('No themes are available to install.')?></p>
		<? } ?>

		<? foreach ($themesArray as $t) { ?>
			<div class="ccm-block-type">
			<table>
			<tr>
				<td class="ccm-template-content" colspan="2"><h3><?=$t->getThemeName()?></h3></td>
			</tr>
			<tr>
				<td class="ccm-template-content" colspan="2"><?=$t->getThemeThumbnail()?></td>
			</tr>
			<tr>
				<td style="color: #aaa; padding: 2px 0 6px"><?=$t->getThemeDescription()?></td>
				<td style="vertical-align: bottom"><?=$ch->button(t("Install"), $this->url('/dashboard/pages/themes','install', $t->getThemeHandle()), "right");?></td>
			</tr>
			</table>
			</div>
		<? } ?>

		</div>

	</div>

	</div>
	<div style="float: left; width: 258px">

	<div class="ccm-module" style="width: 248px; margin-bottom: 0px; clear: left">

		<h1><span><?=t('Marketplace')?></span></h1>
		<div class="ccm-dashboard-inner">
		<? if (!UserInfo::isRemotelyLoggedIn()) { ?>
			<p>You aren't currently signed in to the marketplace.</p>
			<p><a onclick="ccmPopupLogin.show('', loginSuccess, '', 1)">Click here to sign in or create an account.</a></p>
		<? } else { ?>
			<p><?=t('You are currently signed in to the marketplace as');?>
          	  <a href="<?=CONCRETE5_ORG_URL ?>/profile/-/<?=UserInfo::getRemoteAuthUserId() ?>/" ><?=UserInfo::getRemoteAuthUserName() ?></a>
			  <?=t('(Not your account? <a onclick="ccm_support.signOut(logoutSuccess)">Sign Out</a>)')?></p>
			<br/><h2><?=t('Featured Add-Ons')?></h2>
			<div style="margin:0px; padding:0px;  height:auto" >	
			<? foreach ($featuredBlocks as $fb) { ?>
				<div class="ccm-block-type">
				<table>
				<tr>
					<td colspan="2"><p class="ccm-block-type-inner" style="background-image: url(<?=$fb->getRemoteIconURL()?>)"><?=$fb->btName?></p></td>
				</tr>
				<tr>
					<td style="color: #aaa; padding: 2px 0 6px"><?=$fb->btDescription?></td>
				<? $file = $fb->getRemoteFileURL();
				   if (!empty($file) && intval($fb->getPrice()) == 0) { ?>
					<td style="vertical-align: bottom"><?=$ch->button(t("Install"), View::url('/dashboard/install', 'remote_addon', $fb->getHandle()), "right");?></td>
				<? } else { ?>
					<td style="vertical-align: bottom"><?=$ch->button(t("Download"), $fb->getRemoteURL(), "right");?></td>
				<? } ?>
				</tr>
				</table>
				</div>
			<? } ?>
			</div>

			<br/><h2><?=t('Featured Themes')?></h2>
			<div style="margin:0px; padding:0px;  height:auto" >	
			<? foreach ($featuredThemes as $ft) { ?>
				<div class="ccm-block-type">
				<table>
				<tr>
					<td class="ccm-template-content" colspan="2"><h3><?=$ft->getThemeName()?></h3></td>
				</tr>
				<tr>
					<td class="ccm-template-content" colspan="2"><img src="<?=$ft->getThemeThumbnail()?>"></td>
				</tr>
				<tr>
					<td style="color: #aaa; padding: 2px 0 6px"><?=$ft->getThemeDescription()?></td>
				<? $file = $ft->getRemoteFileURL();
				   if (!empty($file) && intval($ft->getPrice()) == 0) { ?>
					<td style="vertical-align: bottom"><?=$ch->button(t("Install"), View::url('/dashboard/install', 'remote_theme', $ft->getHandle()), "right");?></td>
				<? } else { ?>
					<td style="vertical-align: bottom"><?=$ch->button(t("Download"), $ft->getRemoteURL(), "right");?></td>
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
	</div>

<? } ?>
