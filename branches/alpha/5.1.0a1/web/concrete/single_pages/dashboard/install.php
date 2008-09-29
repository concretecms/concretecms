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


<? if ($this->controller->get('nav') == 'packages') { ?>

	
	<h1><span>Applications and Packages</span></h1>
	<div class="ccm-dashboard-inner">
	
	<h2>Installed Applications</h2>
		<? 
		if (count($pkgArray) == 0) { ?>
		<p>No packages have been installed.</p>
		<? } else { ?>
		
		<div style="margin:0px; padding:0px; height:auto" >	
		<?	foreach ($pkgArray as $pkg) { ?>
			<div class="ccm-block-type">
				<p class="ccm-block-type-inner" style="background-image: url(<?=$ci->getPackageIconURL($pkg)?>)" href="<?=$this->url('/dashboard/install', 'install_package', $pkg->getPackageHandle())?>"><?=$pkg->getPackageName()?></p>
				<div class="ccm-block-type-description"  style="display: block"><?=$pkg->getPackageDescription()?></div>
			</div>
			<? } ?>
		</table>
		</div>
	<? } ?>
	
	<br/>
	<h2>Available Applications. Click to Install.</h2>
		<? if (count($pkgAvailableArray) == 0) { ?>
		No packages are available.
	
		<? } else { ?>
		<div style="margin:0px; padding:0px;  height:auto" >	
		<?	foreach ($pkgAvailableArray as $pkg) { ?>
			<div class="ccm-block-type">
				<a class="ccm-block-type-inner" style="background-image: url(<?=$ci->getPackageIconURL($pkg)?>)" href="<?=$this->url('/dashboard/install', 'install_package', $pkg->getPackageHandle())?>"><?=$pkg->getPackageName()?></a>
				<div class="ccm-block-type-description"  style="display: block"><?=$pkg->getPackageDescription()?></div>
			</div>
			<? } ?>
		</div>
	<? } ?>
	</form>
	
	</div>


<? } else { ?>

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
			<h2>Available Block Types. Click to Install.</h2>
			
			<? 
			if (count($btArray2) == 0) { ?>
				No local block types available.
			<? 	
			} else { ?>
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

<? } ?>