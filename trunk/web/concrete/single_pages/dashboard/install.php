<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$btArray = BlockTypeList::getInstalledList();
$btArray2 = BlockTypeList::getAvailableList();
$ih = Loader::helper('concrete/interface');

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
	$message = 'Block Type Installed';
}

$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');
?>


<? if (is_object($bt)) { ?>
		<h1><span><?=$bt->getBlockTypeName()?></span></h1>
		<div class="ccm-dashboard-inner">
		<img src="<?=$ci->getBlockTypeIconURL($bt)?>" style="float: right" />
		<div><a href="<?=$this->url('/dashboard/install')?>">&lt; Return to Block Types</a></div><br/>
		
		<h2>Description</h2>
		<p><?=$bt->getBlockTypeDescription()?></p>

		<h2>Usage Count</h2>
		<p><?=$num?></p>
		
		<? if ($bt->isBlockTypeInternal()) { ?>
		<h2>Internal</h2>
		<p>This is an internal block type.</p>
		<? } ?>

		<?
		$buttons[] = $ch->button("Refresh", $this->url('/dashboard/install','refresh_block_type', $bt->getBlockTypeID()), "left");
		if ($bt->canUnInstall()) {
			$buttons[] = $ch->button("Remove", $this->url('/dashboard/install', 'uninstall_block_type', $bt->getBlockTypeID()), "left");
		}

		print $ch->buttons($buttons); ?>
	
	</div>
		
		
	
	
	<? } else { ?>
		<h1><span>Block Types</span></h1>
		<div class="ccm-dashboard-inner">
	
		<h2>Installed Block Types</h2>
		<? 
		if (count($btArray) == 0) { ?>
			<p>No block types have been installed.</p>
		<? } else { ?>
		
		<div style="margin:0px; padding:0px; height:auto" >	
	
		<?	foreach ($btArray as $bt) { ?>
			<div class="ccm-block-type">
				<a class="ccm-block-type-inner" style="background-image: url(<?=$ci->getBlockTypeIconURL($bt)?>)" href="<?=$this->url('/dashboard/install', 'inspect_block_type', $bt->getBlockTypeID())?>"><?=$bt->getBlockTypeName()?></a>
				<div class="ccm-block-type-description"  id="ccm-bt-help<?=$bt->getBlockTypeID()?>" style="display: block"><?=$bt->getBlockTypeDescription()?></div>
			</div>
		<? } ?>
			
		<? } ?>
		
		<br/>
		<h2>Block Types Available for Installation. Click to Install.</h2>
		
		<? 
		if (count($btArray2) == 0) { ?>
			No local block types available.
		<? 	
		} else { ?>
			<div style="margin:0px; padding:0px;  height:auto" >	
			<table border="0" cellspacing="0" cellpadding="0">
			<?
			foreach ($btArray2 as $bt) { ?>
			<div class="ccm-block-type">
				<a class="ccm-block-type-inner" style="background-image: url(<?=$ci->getBlockTypeIconURL($bt)?>)" href="<?=$this->url('/dashboard/install', 'install_block_type', $bt->getBlockTypeHandle())?>"><?=$bt->getBlockTypeName()?></a>
				<div class="ccm-block-type-description"  style="display: block"><?=$bt->getBlockTypeDescription()?></div>
			</div>
			<? } ?>
			
		</table>
		</div>
		</div>
		
		<? } ?>
	
	<? } ?>	
<? /*
// These are not quite ready yet.

<h1><span>Packages</span></h1>
<div class="ccm-dashboard-inner">

<h2>Installed Packages</h2>
	<? 
	if (count($pkgArray) == 0) { ?>
	<p>No packages have been installed.</p>
	<? } else { ?>
	
	<div style="margin:0px; padding:0px; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<td class="subheader">Name</td>
		<td class="subheader">Description</td>
		<td class="subheader">Date Installed</td>
	</tr>
	<?	foreach ($pkgArray as $pkg) { ?>
		<tr>
			<td><?=$pkg->getPackageName()?></td>
			<td><?=$pkg->getPackageDescription()?></td>
			<td><?=$pkg->getPackageDateInstalled()?></td>
		</tr>
		<? } ?>
	</table>
	</div>
<? } ?>

<br/>
<h2>Available Packages</h2>
	<? if (count($pkgAvailableArray) == 0) { ?>
	No packages are available.

	<? } else { ?>
	<div style="margin:0px; padding:0px;  height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<?	foreach ($pkgAvailableArray as $pkg) { ?>
		<tr>
			<td style="white-space: nowrap"><?=$pkg->getPackageName()?></td>
			<td ><?=$pkg->getPackageDescription()?></td>
			<td  style="width:70px;"><?=$ih->button('Install', $this->url('/dashboard/install', 'install_package', $pkg->getPackageHandle()), 'left')?></td>
		</tr>
		<? } ?>
	</table>
	</div>
<? } ?>
</form>

</div>
*/ ?>