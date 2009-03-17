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
	$message = t('Block Type Installed');
}

$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');

$btArray = BlockTypeList::getInstalledList();
$btAvailableArray = BlockTypeList::getAvailableList();
//$pkgArray = Package::getInstalledList();
$pkgAvailableArray = Package::getAvailablePackages();
$installedArray = $btArray;
$availableArray = array_merge($btAvailableArray, $pkgAvailableArray);
ksort($availableArray);
$themesArray = PageTheme::getAvailableThemes();

?>

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

		<h1><span><?=t('Installed Block Types')?></span></h1>
		<div class="ccm-dashboard-inner">
    	<h2><?php echo t('Click to View Details.')?></h2>
		
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

		</div>
			
	</div>

	<div class="ccm-module" style="width: 250px; margin-bottom: 0px">

		<h1><span><?=t('Available Block Types')?></span></h1>
		<div class="ccm-dashboard-inner">

		<? if (count($availableArray) == 0) { ?>

		<?=t('No added functionality is available to install.')?>
	
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
				<td colspan="2" style="color: #aaa; padding: 2px 0 6px"><?=$obj->getBlockTypeDescription()?></td>
				<td><?=$ch->button(t("Install"), $this->url('/dashboard/install','install_block_type', $obj->getBlockTypeHandle()), "left");?></td>
			<? } else { ?>
				<td colspan="2" style="color: #aaa; padding: 2px 0 6px"><?=$obj->getPackageDescription()?></td>
				<td><?=$ch->button(t("Install"), $this->url('/dashboard/install','install_package', $obj->getPackageHandle()), "left");?></td>
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
			<p><?=t('No themes are available.')?></p>
		<? } ?>

		<? foreach ($themesArray as $t) { ?>
			<div class="ccm-block-type">
			<table>
			<tr>
				<td class="ccm-template-content" colspan="2"><h3><?=$t->getThemeName()?></h3></td>
			</tr>
			<tr>
				<td class="ccm-template-content"><?=$t->getThemeThumbnail()?></td>
				<td><?=$ch->button(t("Install"), $this->url('/dashboard/pages/themes','install', $t->getThemeHandle()), "left");?></td>
			</tr>
			<tr>
				<td colspan="2" style="color: #aaa; padding: 2px 0 6px"><?=$t->getThemeDescription()?></td>
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
			<p>You aren't currently signed in to the marketplace.
			  <a href="#">Click here to sign in or create an account.</a></p>
		</div>

	</div>

	</div>
	</div>

<? } ?>
