<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$btArray = BlockTypeList::getInstalledList();
$btArray2 = BlockTypeList::getAvailableList();
$ih = Loader::helper('concrete/interface');
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
?>


<?php  if ($this->controller->get('nav') == 'packages') { ?>

	
	<h1><span><?php echo t('Applications and Packages')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<h2><?php echo t('Installed Applications')?></h2>
		<?php  
		if (count($pkgArray) == 0) { ?>
		<p><?php echo t('No packages have been installed.')?></p>
		<?php  } else { ?>
		
		<div style="margin:0px; padding:0px; height:auto" >	
		<?php 	foreach ($pkgArray as $pkg) { ?>
			<div class="ccm-block-type">
				<p class="ccm-block-type-inner" style="background-image: url(<?php echo $ci->getPackageIconURL($pkg)?>)" href="<?php echo $this->url('/dashboard/install', 'install_package', $pkg->getPackageHandle())?>"><?php echo $pkg->getPackageName()?></p>
				<div class="ccm-block-type-description"  style="display: block"><?php echo $pkg->getPackageDescription()?></div>
			</div>
			<?php  } ?>
		</table>
		</div>
	<?php  } ?>
	
	<br/>
	<h2><?php echo t('Available Applications. Click to Install.')?></h2>
		<?php  if (count($pkgAvailableArray) == 0) { ?>
		<?php echo t('No packages are available.')?>
	
		<?php  } else { ?>
		<div style="margin:0px; padding:0px;  height:auto" >	
		<?php 	foreach ($pkgAvailableArray as $pkg) { ?>
			<div class="ccm-block-type">
				<a class="ccm-block-type-inner" style="background-image: url(<?php echo $ci->getPackageIconURL($pkg)?>)" href="<?php echo $this->url('/dashboard/install', 'install_package', $pkg->getPackageHandle())?>"><?php echo $pkg->getPackageName()?></a>
				<div class="ccm-block-type-description"  style="display: block"><?php echo $pkg->getPackageDescription()?></div>
			</div>
			<?php  } ?>
		</div>
	<?php  } ?>
	</form>
	
	</div>


<?php  } else { ?>

	<?php  if (is_object($bt)) { ?>
			<h1><span><?php echo $bt->getBlockTypeName()?></span></h1>
			<div class="ccm-dashboard-inner">
			<img src="<?php echo $ci->getBlockTypeIconURL($bt)?>" style="float: right" />
			<div><a href="<?php echo $this->url('/dashboard/install')?>">&lt; <?php echo t('Return to Block Types')?></a></div><br/>
			
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
			<h1><span><?php echo t('Block Types')?></span></h1>
			<div class="ccm-dashboard-inner">
		
			<h2><?php echo t('Installed Block Types')?></h2>
			<?php  
			if (count($btArray) == 0) { ?>
				<p><?php echo t('No block types have been installed.')?></p>
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
			<h2><?php echo t('Available Block Types. Click to Install.')?></h2>
			
			<?php  
			if (count($btArray2) == 0) { ?>
				<?php echo t('No local block types available.')?>
			<?php  	
			} else { ?>
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

<?php  } ?>