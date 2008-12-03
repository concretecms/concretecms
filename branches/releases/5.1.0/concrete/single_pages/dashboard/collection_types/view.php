<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('collection_types');
Loader::model('single_page');
Loader::model('collection_attributes');
$attribs = CollectionAttributeKey::getList();
$ih = Loader::helper('concrete/interface');

$ctArray = CollectionType::getList();
$args['section'] = 'collection_types';
$u = new User();

if ($_GET['cID'] && $_GET['task'] == 'load_master') { 
	$u->loadMasterCollectionEdit($_GET['cID'], 1);
	header('Location: ' . BASE_URL . DIR_REL . '/index.php?cID=' . $_GET['cID'] . '&mode=edit');
}

$icons = CollectionType::getIcons();

if ($_REQUEST['task'] == 'edit') {
	$ct = CollectionType::getByID($_REQUEST['ctID']);
	if (is_object($ct)) { 		
		if ($_POST['update']) {
		
			$ctName = $_POST['ctName'];
			$ctHandle = $_POST['ctHandle'];
			
		} else {
			
			$ctName = $ct->getCollectionTypeName();
			$ctHandle = $ct->getCollectionTypeHandle();
		
		}
		
		$ctEditMode = true;
	}
}

if ($_POST['task'] == 'add' || $_POST['update']) {
	$ctName = $_POST['ctName'];
	$ctHandle = $_POST['ctHandle'];
	
	$error = array();
	if (!$ctHandle) {
		$error[] = t("Handle required.");
	}
	if (!$ctName) {
		$error[] = t("Name required.");
	}
	
	$akIDArray = $_POST['akID'];
	if (!is_array($akIDArray)) {
		$akIDArray = array();
	}
	
	if (count($error) == 0) {
		try {
			if ($_POST['task'] == 'add') {
				$nCT = CollectionType::add($_POST);
				$this->controller->redirect('/dashboard/collection_types?created=1');
			} else if (is_object($ct)) {
				$ct->update($_POST);
				$this->controller->redirect('/dashboard/collection_types?updated=1');
			}		
			exit;
		} catch(Exception $e1) {
			$error[] = $e1->getMessage();
		}
	}
} else {
	if ($_REQUEST['p'] && $_REQUEST['task'] == 'refresh') { 
		$p = SinglePage::getByID($_REQUEST['p']);
		$p->refresh();
		$this->controller->redirect('/dashboard/collection_types?refreshed=1');
		exit;
	}
	
	if ($_POST['add_static_page']) {
		$pathToNode = SinglePage::getPathToNode($_POST['pageURL'], false);
		$path = SinglePage::sanitizePath($_POST['pageURL']);
		
		if (strlen($pathToNode) > 0) {
			// now we check to see if this is already added
			$pc = Page::getByPath('/' . $path, 'RECENT');
			
			if ($pc->getError() == COLLECTION_NOT_FOUND) {
				SinglePage::add($_POST['pageURL']);
				$this->controller->redirect('/dashboard/collection_types?page_created=1');
			} else {
				$error[] = t("That page has already been added.");
			}
		} else {
			$error[] = t('That specified path doesn\'t appear to be a valid static page.');
		}
		
		

	}
	$generated = SinglePage::getList();
}

if ($_REQUEST['created']) { 
	$message = t('Page Type added.');
} else if ($_REQUEST['updated']) {
	$message = t('Page Type updated.');
} else if ($_REQUEST['refreshed']) {
	$message = t('Page refreshed.');
} else if ($_REQUEST['page_created']) {
	$message = t('Static page created.');
}

if ($_REQUEST['attribute_updated']) {
	$message = t('Page Attribute Updated.');
}
if ($_REQUEST['attribute_created']) {
	$message = t('Page Attribute Created.');
}
if ($_REQUEST['attribute_deleted']) {
	$message = t('Page Attribute Deleted.');
}

?>

<?php 
if ($ctEditMode) { 
	$ct->populateAvailableAttributeKeys();
	?>	

	<h1><span><?php echo t('Edit Page Type')?> (<em class="required">*</em> - <?php echo t('required field')?>)</span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<form method="post" id="update_page_type" action="<?php echo $this->url('/dashboard/collection_types/')?>">
	<input type="hidden" name="ctID" value="<?php echo $_REQUEST['ctID']?>" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="update" value="1" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" colspan="2"><?php echo t('Name')?> <span class="required">*</span></td>
		<td class="subheader"><?php echo t('Handle')?> <span class="required">*</span></td>
	</tr>
	<tr>
		<td style="width: 65%" colspan="2"><input type="text" name="ctName" style="width: 100%" value="<?php echo $ctName?>" /></td>
		<td style="width: 35%"><input type="text" name="ctHandle" style="width: 100%" value="<?php echo $ctHandle?>" /></td>
	</tr>
	<tr>
		<td colspan="3" class="subheader"><?php echo t('Icon')?></td>
	</tr>
	<tr>
		<td colspan="3">
		<?php  
		$first = true;
		foreach($icons as $ic) { ?>
			<?php 
			$checked = false;
			if ($ct->getCollectionTypeIcon() == $ic || $first) { 
				$checked = 'checked';
			}
			$first = false;
			?>
			<span style="white-space: nowrap; margin-right: 20px">
			<input type="radio" name="ctIcon" value="<?php echo $ic?>" style="vertical-align: middle" <?php echo $checked?> />
			<img src="<?php echo REL_DIR_FILES_COLLECTION_TYPE_ICONS?>/<?php echo $ic?>" style="vertical-align: middle" /></span>
		<?php  } ?>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="header"><?php echo t('Available Metadata Attributes')?></td>
	</tr>
	<?php 
		$attribs = CollectionAttributeKey::getList();
		$i = 0;
		foreach($attribs as $ak) { 
		if ($i == 0) { ?>
			<tr>
		<?php  } ?>
		
		<td><input type="checkbox" name="akID[]" value="<?php echo $ak->getCollectionAttributeKeyID()?>" <?php  if (($this->controller->isPost() && in_array($ak->getCollectionAttributeKeyID(), $akIDArray))) { ?> checked <?php  } else if ((!$this->controller->isPost()) && $ct->isAvailableCollectionTypeAttribute($ak->getCollectionAttributeKeyID())) { ?> checked <?php  } ?> /> <?php echo $ak->getCollectionAttributeKeyName()?></td>
		
		<?php  $i++;
		
		if ($i == 3) { ?>
		</tr>
		<?php  
		$i = 0;
		}
		
	}
	
	if ($i < 3 && $i > 0) {
		for ($j = $i; $j < 3; $j++) { ?>
			<td>&nbsp;</td>
		<?php  }
	?></tr>
	<?php  } ?>
	<tr>
		<td colspan="3" class="header">
		<?php  print $ih->submit(t('Update Page Type'), 'update_page_type', 'right');?>
		<?php  print $ih->button(t('Cancel'), $this->url('/dashboard/collection_types'), 'left');?>
		</td>
	</tr>
    </table>
	</div>
	
	<br>
	</form>	
	</div>
	
	<h1><span><?php echo t('Delete Page Type')?></span></h1>
	<div class="ccm-dashboard-inner">


	<p><?php echo t('Click below to remove this page type entirely. (Note: You may only remove page types which are not being used on your site. If a page type is being used, delete all instances of its pages first.)')?> 
	<div class="ccm-spacer">&nbsp;</div>
	
	<?php  print $ih->button_js(t('Delete Page Type'), "deletePageType", 'left');?>
	
	<div class="ccm-spacer">&nbsp;</div>
	<?php 
	$confirmMsg = t('Are you sure?');
	?>
	<script type="text/javascript">
	deletePageType = function() {
		if(confirm('<?php echo $confirmMsg?>')){ 
			location.href="<?php echo $this->url('/dashboard/collection_types/','delete',$_REQUEST['ctID'])?>";
		}	
	}
	</script>
	</div>
	
<?php  
} else if ($_REQUEST['task'] == 'add') {  ?>
	
	<h1><span><?php echo t('Add Page Type')?> (<em class="required">*</em> - <?php echo t('required field')?>)</span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<form method="post" id="add_page_type" action="<?php echo $this->url('/dashboard/collection_types/')?>">
	<input type="hidden" name="task" value="add" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" colspan="2"><?php echo t('Name')?> <span class="required">*</span></td>
		<td class="subheader"><?php echo t('Handle')?> <span class="required">*</span></td>
	</tr>	
	<tr>
		<td style="width: 65%"  colspan="2"><input type="text" name="ctName" style="width: 100%" value="<?php echo $_POST['ctName']?>" /></td>
		<td style="width: 35%"><input type="text" name="ctHandle" style="width: 100%" value="<?php echo $_POST['ctHandle']?>" /></td>
	</tr>
	<tr>
		<td colspan="3" class="subheader"><?php echo t('Icon')?></td>
	</tr>
	<tr>
		<td colspan="3">
		<?php  
		$first = true;
		foreach($icons as $ic) { ?>
			<?php 
			$checked = false;
			if ($first) { 
				$checked = 'checked';
			}
			$first = false;
			?>
			<span style="white-space: nowrap; margin-right: 20px">
			<input type="radio" name="ctIcon" value="<?php echo $ic?>" style="vertical-align: middle" <?php echo $checked?> />
			<img src="<?php echo REL_DIR_FILES_COLLECTION_TYPE_ICONS?>/<?php echo $ic?>" style="vertical-align: middle" /></span>
		<?php  } ?>
		</td>
	</tr>
	<tr>
		<td colspan="3"><?php echo t('Available Metadata Attributes')?></td>
	</tr>
	<?php 
		$attribs = CollectionAttributeKey::getList();
		$i = 0;
		foreach($attribs as $ak) { 
		if ($i == 0) { ?>
			<tr>
		<?php  } ?>
		
		<td><input type="checkbox" name="akID[]" value="<?php echo $ak->getCollectionAttributeKeyID()?>" /> <?php echo $ak->getCollectionAttributeKeyName()?></td>
		
		<?php  $i++;
		
		if ($i == 3) { ?>
		</tr>
		<?php  
		$i = 0;
		}
		
	}
	
	if ($i < 3 && $i > 0) {
		for ($j = $i; $j < 3; $j++) { ?>
			<td>&nbsp;</td>
		<?php  }
	?></tr>
	<?php  } ?>
	<tr>
		<td colspan="3" class="header">
		<?php  print $ih->submit(t('Add Page Type'), 'add_page_type', 'right');?>
		<?php  print $ih->button(t('Cancel'), $this->url('/dashboard/collection_types'), 'left');?>
		</td>
	</tr>
	</table>
	</div>
	
	<br>
	</form>	
	</div>

<?php 
} else { ?>

	<h1><span><?php echo t('Page Types')?></span></h1>
	<div class="ccm-dashboard-inner">
	

	<?php  if (count($ctArray) == 0) { ?>
		<br/><strong><?php echo t('No page types found.')?></strong><br/><br>
	<?php  } else { ?>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list" width="600">
	<tr>
		<td class="subheader" width="100%"><?php echo t('Name')?></td>
		<td class="subheader"><?php echo t('Handle')?></td>
		<td class="subheader"><?php echo t('Package')?></td>
		<td class="subheader"><div style="width: 90px"></div></td>
		<td class="subheader"><div style="width: 60px"></div></td>
	</tr>
	<?php  foreach ($ctArray as $ct) { ?>
	<tr>
		<td><?php echo $ct->getCollectionTypeName()?></td>
		<td><?php echo $ct->getCollectionTypeHandle()?></td>
		<td><?php 
			if ($ct->getPackageID() > 0) {
				$package = Package::getByID($ct->getPackageID());
				print $package->getPackageName(); 
			} else {
				print t('None');
			}
			?></td>
		<td>
		<?php  if ($ct->getMasterCollectionID()) {?>
			<?php  print $ih->button_js(t('Defaults'), "window.open('" . $this->url('/dashboard/collection_types?cID=' . $ct->getMasterCollectionID() . '&task=load_master')."')", 'left', false, array('title'=>t('Lets you set default permissions and blocks for a particular page type.')) );?>
		<?php  } ?>
	
		</td>
		<td><?php  print $ih->button(t('Edit'), $this->url('/dashboard/collection_types?ctID=' . $ct->getCollectionTypeID() . '&task=edit'))?></td>

	</tr>
	<?php  } ?>
	
	</table>
	</div>
	
	<?php  } ?>
	
	<br/>
	<div class="ccm-buttons">
		<a class="ccm-button" href="<?php echo $this->url('/dashboard/collection_types?task=add')?>"><span><em class="ccm-button-add"><?php echo t('Add a Page Type')?></em></span></a>	
	</div>
	<div class="ccm-spacer">&nbsp;</div>

	</div>
	
	
	<h1><span><?php echo t('Page Attributes')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<?php  if (count($attribs) > 0) { ?>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<td class="subheader" width="100%"><?php echo t('Name')?></td>
		<td class="subheader"><?php echo t('Handle')?></td>
		<td class="subheader"><div style="width: 60px"></div></td>
		<td class="subheader"><div style="width: 70px"></div></td>
	</tr>
	<?php 
	foreach($attribs as $ak) { ?>
	<tr>
		<td><?php echo $ak->getCollectionAttributeKeyName()?></td>
		<td style="white-space: nowrap"><?php echo $ak->getCollectionAttributeKeyHandle()?></td>
		<td><?php  print $ih->button(t('Edit'), $this->url('/dashboard/collection_types/attributes?akID=' . $ak->getCollectionAttributeKeyID() . '&task=edit'))?></td>
		<td><?php  print $ih->button(t('Delete'), "javascript:if (confirm('".t('Are you sure you wish to delete this attribute?')."')) { location.href='" . $this->url('/dashboard/collection_types/attributes?akID=' . $ak->getCollectionAttributeKeyID() . '&task=delete') . "' }")?></td>
	</tr>
	<?php  } ?>
	</table>
	</div>
	
	<?php  } else { ?>
		
	<br/><strong><?php echo t('No page attributes defined.')?></strong><br/><br/>
		
	<?php  } ?>
	
	<br/>
	<div class="ccm-buttons">
		<a class="ccm-button" href="<?php echo $this->url('/dashboard/collection_types/attributes')?>"><span><?php echo t('Add Page Attribute')?></span></a>	
	</div>
	<div class="ccm-spacer">&nbsp;</div>

	</div>
	
	

	<h1><span><?php echo t('Single Pages')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list" width="600">
	<tr>
		<td colspan="4" class="header"><?php echo t('Already Installed')?></td>
	</tr>
	<tr>
		<td class="subheader" width="100%"><?php echo t('Name')?></td>
		<td class="subheader"><?php echo t('Path')?></td>
		<td class="subheader"><?php echo t('Package')?></td>
		<td class="subheader"><div style="width: 90px"></div></td>
	</tr>
	<?php  if (count($generated) == 0) { ?>
		<td colspan="4"><?php echo t('No pages found.')?></td>
	<?php  } else { ?>
	
	<?php  foreach ($generated as $p) { ?>
	<?php 
		if ($p->getPackageID() > 0) {
			$package = Package::getByID($p->getPackageID());
			$packageHandle = $package->getPackageHandle();
			$packageName = $package->getPackageName();
		} else {
			$packageName = t('None');
		}
		
	?>
	<tr <?php  if ($packageHandle == DIRNAME_PACKAGE_CORE) { ?> class="ccm-core-package-row" <?php  } ?>>
		<td><a href="<?php echo DIR_REL?>/index.php?cID=<?php echo $p->getCollectionID()?>"><?php echo $p->getCollectionName()?></a></td>
		<td><?php echo $p->getCollectionPath()?></td>
		<td><?php  print $packageName; ?></td>
		<td>
			<?php  print $ih->button(t('Refresh'),$this->url('/dashboard/collection_types/?p=' . $p->getCollectionID() . '&task=refresh'), 'left', false, array('title'=>t('Refreshes the page, rebuilding its permissions and its name.')));?>
		</td>
	</tr>
	<?php  }
	
	} ?>
	<tr>
		<td colspan="4" class="header"><?php echo t('Add Single Page')?></td>
	</tr>
	<tr>
		<td colspan="4"><?php echo t('The page you want to add is available at:')?>
		<br>
		<form method="post" id="add_static_page_form" action="<?php echo $this->url('/dashboard/collection_types/')?>">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td>
		<?php echo BASE_URL?>/<input type="text" name="pageURL" value="<?php echo $_POST['pageURL']?>" style="width: 200px" /></td>
		<td>
		<?php  print $ih->submit(t('Add'), 'add_static_page_form', 'left');?></td>
		</tr>
		</table>
		
		<input type="hidden" value="1" name="add_static_page" />
		</form>
		</td>
	</tr>
	
	
	</table>
	</div>
	</div>
	
	
<script type="text/javascript">
$(function() {
	$("#ccm-toggle-pages").click(function() {
		if (this.checked) {
			$("tr.ccm-core-package-row").css('display', 'none');
		} else {
			$("tr.ccm-core-package-row").css('display', 'table-row');
		}
	});
});
</script>

<?php  }