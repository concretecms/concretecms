<?php 
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


<?php  if (is_object($bt)) { ?>
		<h1><span><?php echo $bt->getBlockTypeName()?></span></h1>
		<div class="ccm-dashboard-inner">
		<img src="<?php echo $ci->getBlockTypeIconURL($bt)?>" style="float: right" />
		<div><a href="<?php echo $this->url('/dashboard/install')?>">&lt; Return to Block Types</a></div><br/>
		
		<h2>Description</h2>
		<p><?php echo $bt->getBlockTypeDescription()?></p>

		<h2>Usage Count</h2>
		<p><?php echo $num?></p>
		
		<?php  if ($bt->isBlockTypeInternal()) { ?>
		<h2>Internal</h2>
		<p>This is an internal block type.</p>
		<?php  } ?>

		<?php 
		$buttons[] = $ch->button("Refresh", $this->url('/dashboard/install','refresh_block_type', $bt->getBlockTypeID()), "left");
		if ($bt->canUnInstall()) {
			$buttons[] = $ch->button("Remove", $this->url('/dashboard/install', 'uninstall_block_type', $bt->getBlockTypeID()), "left");
		}

		print $ch->buttons($buttons); ?>
	
	</div>
		
		
	
	
	<?php  } else { ?>
		<h1><span>Block Types</span></h1>
		<div class="ccm-dashboard-inner">
	
		<h2>Installed Block Types</h2>
		<?php  
		if (count($btArray) == 0) { ?>
			<p>No block types have been installed.</p>
		<?php  } else { ?>
		
		<div style="margin:0px; padding:0px; height:auto" >	
	
		<?php 	foreach ($btArray as $bt) { ?>
			<div class="ccm-block-type">
				<a class="ccm-block-type-inner" style="background-image: url(<?php echo $ci->getBlockTypeIconURL($bt)?>)" href="<?php echo $this->url('/dashboard/install', 'inspect_block_type', $bt->getBlockTypeID())?>"><?php echo $bt->getBlockTypeName()?></a>
				<div class="ccm-block-type-description"  id="ccm-bt-help<?php echo $bt->getBlockTypeID()?>" style="display: block"><?php echo $bt->getBlockTypeDescription()?></div>
			</div>
		<?php  } ?>
			
		<?php  } ?>
		
		<br/>
		<h2>Block Types Available for Installation. Click to Install.</h2>
		
		<?php  
		if (count($btArray2) == 0) { ?>
			No local block types available.
		<?php  	
		} else { ?>
			<div style="margin:0px; padding:0px;  height:auto" >	
			<table border="0" cellspacing="0" cellpadding="0">
			<?php 
			foreach ($btArray2 as $bt) { ?>
			<div class="ccm-block-type">
				<a class="ccm-block-type-inner" style="background-image: url(<?php echo $ci->getBlockTypeIconURL($bt)?>)" href="<?php echo $this->url('/dashboard/install', 'install_block_type', $bt->getBlockTypeHandle())?>"><?php echo $bt->getBlockTypeName()?></a>
				<div class="ccm-block-type-description"  style="display: block"><?php echo $bt->getBlockTypeDescription()?></div>
			</div>
			<?php  } ?>
			
		</table>
		</div>
		</div>
		
		<?php  } ?>
	
	<?php  } ?>	
<?php  /*
// These are not quite ready yet.

<h1><span>Packages</span></h1>
<div class="ccm-dashboard-inner">

<h2>Installed Packages</h2>
	<?php  
	if (count($pkgArray) == 0) { ?>
	<p>No packages have been installed.</p>
	<?php  } else { ?>
	
	<div style="margin:0px; padding:0px; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<td class="subheader">Name</td>
		<td class="subheader">Description</td>
		<td class="subheader">Date Installed</td>
	</tr>
	<?php 	foreach ($pkgArray as $pkg) { ?>
		<tr>
			<td><?php echo $pkg->getPackageName()?></td>
			<td><?php echo $pkg->getPackageDescription()?></td>
			<td><?php echo $pkg->getPackageDateInstalled()?></td>
		</tr>
		<?php  } ?>
	</table>
	</div>
<?php  } ?>

<br/>
<h2>Available Packages</h2>
	<?php  if (count($pkgAvailableArray) == 0) { ?>
	No packages are available.

	<?php  } else { ?>
	<div style="margin:0px; padding:0px;  height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<?php 	foreach ($pkgAvailableArray as $pkg) { ?>
		<tr>
			<td style="white-space: nowrap"><?php echo $pkg->getPackageName()?></td>
			<td ><?php echo $pkg->getPackageDescription()?></td>
			<td  style="width:70px;"><?php echo $ih->button('Install', $this->url('/dashboard/install', 'install_package', $pkg->getPackageHandle()), 'left')?></td>
		</tr>
		<?php  } ?>
	</table>
	</div>
<?php  } ?>
</form>

</div>
*/ ?>