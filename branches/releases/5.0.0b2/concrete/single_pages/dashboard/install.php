<?
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

?>

	<h1><span>Block Types</span></h1>
	<div class="ccm-dashboard-inner">
	
	<h2>Installed Block Types</h2>
	<? 
	if (count($btArray) == 0) { ?>
		<p>No block types have been installed.</p>
	<? } else { ?>
	
	<div style="margin:0px; padding:0px; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list" >
	<tr>
		<td colspan="4" class="header">Installed Block Types</td>
	</tr>
	<tr>
		<td class="subheader">Name</td>
		<td class="subheader">Handle</td>
		<td class="subheader">Description</td>
		<td class="subheader">&nbsp;</td>
	</tr>
	<?	foreach ($btArray as $bt) { ?>
		<tr>
			<td style="white-space: nowrap"><?=$bt->getBlockTypeName()?></td>
			<td><?=$bt->getBlockTypeHandle()?></td>
			<td><?=$bt->getBlockTypeDescription()?></td>
			<td style="width:70px;">
				<form method="post" id="install_blocktype_<?=$bt->getBlockTypeHandle()?>" action="<?=$this->url('/dashboard/install/')?>">
					<input type="hidden" name="task" value="install_blocktype" />
					<input type="hidden" name="pkgID" value="<?=$bt->getPackageID()?>" />
					<? print $ih->submit('Refresh', 'install_blocktype_' . $bt->getBlockTypeHandle(), 'left', false, array('title'=>'Grabs the latest name and database file from the block.'));?>
					<input type="hidden" name="btHandle" value="<?=$bt->getBlockTypeHandle()?>" />
					<input type="hidden" name="btID" value="<?=$bt->getBlockTypeID()?>" />
				</form>
			</td>
		</tr>
		<? } ?>
		
	</table>
	</div>
	<? } ?>
	
	<br/>
	<h2>Block Types Available for Installation</h2>
	
	<? 
	if (count($btArray2) == 0) { ?>
		No local block types available.
	<? 	
	} else { ?>
		<div style="margin:0px; padding:0px;  height:auto" >	
		<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
		<?
		foreach ($btArray2 as $bt) { ?>
		<tr>
			<td style="white-space: nowrap"><?=$bt->getBlockTypeName()?></td>
			<td><?=$bt->getBlockTypeHandle()?></td>
			<td><?=$bt->getBlockTypeDescription()?></td>
			<td  style="width:70px;"><? if ($bt->isInstalled()) { ?>
				<form method="post" action="<?=$this->url('/dashboard/install/')?>" id="install_blocktype_<?=$bt->getBlockTypeHandle()?>"><input type="hidden" name="task" value="install_blocktype" /><input type="hidden" name="type" value="LOCAL" /><?=$ih->submit('Refresh', 'install_blocktype_' . $bt->getBlockTypeHandle(), 'left')?><input type="submit" value="Refresh" /><input type="hidden" name="btHandle" value="<?=$bt->getBlockTypeHandle()?>" /><input type="hidden" name="btID" value="<?=$bt->getBlockTypeID()?>" /></form>
			<? } else { ?>
				<form method="post" action="<?=$this->url('/dashboard/install/')?>" id="install_blocktype_<?=$bt->getBlockTypeHandle()?>"><input type="hidden" name="task" value="install_blocktype" /><input type="hidden" name="type" value="LOCAL" /><?=$ih->submit('Install', 'install_blocktype_' . $bt->getBlockTypeHandle(), 'left')?><input type="hidden" name="btHandle" value="<?=$bt->getBlockTypeHandle()?>" />
				</form>
			<? } ?>
			</td>
		</tr>
		<? } ?>
		
	</table>
	</div>
	
	<? } ?>
	</div>
	
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