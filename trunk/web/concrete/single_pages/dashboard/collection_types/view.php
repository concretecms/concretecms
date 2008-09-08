<?
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

if ($_POST['add'] || $_POST['update']) {
	$ctName = $_POST['ctName'];
	$ctHandle = $_POST['ctHandle'];
	
	$error = array();
	if (!$ctHandle) {
		$error[] = "Handle required.";
	}
	if (!$ctName) {
		$error[] = "Name required.";
	}
	
	if (count($error) == 0) {
		if ($_POST['add']) {
			$nCT = CollectionType::add($_POST);
			$this->controller->redirect('/dashboard/collection_types?created=1');
		} else if (is_object($ct)) {
			$ct->update($_POST);
			$this->controller->redirect('/dashboard/collection_types?updated=1');
		}		
		exit;
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
				$error[] = "That page has already been added.";
			}
		} else {
			$error[] = 'That specified path doesn\'t appear to be a valid static page.';
		}
		
		

	}
	$generated = SinglePage::getList();
}

if ($_REQUEST['created']) { 
	$message = 'Page Type added.';
} else if ($_REQUEST['updated']) {
	$message = 'Page Type updated.';
} else if ($_REQUEST['refreshed']) {
	$message = 'Page refreshed.';
} else if ($_REQUEST['page_created']) {
	$message = 'Static page created.';
}

if ($_REQUEST['attribute_updated']) {
	$message = 'User Attribute Updated.';
}
if ($_REQUEST['attribute_created']) {
	$message = 'User Attribute Created.';
}
if ($_REQUEST['attribute_deleted']) {
	$message = 'User Attribute Deleted.';
}

?>

<?
if ($ctEditMode) { 
	$ct->populateAvailableAttributeKeys();
	?>	

	<h1><span>Edit Page Type (<em class="required">*</em> - required field)</span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<form method="post" action="<?=$this->url('/dashboard/collection_types/')?>">
	<input type="hidden" name="ctID" value="<?=$_REQUEST['ctID']?>" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="update" value="1" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" colspan="2">Name <span class="required">*</span></td>
		<td class="subheader">Handle <span class="required">*</span></td>
	</tr>
	<tr>
		<td style="width: 65%" colspan="2"><input type="text" name="ctName" style="width: 100%" value="<?=$ctName?>" /></td>
		<td style="width: 35%"><input type="text" name="ctHandle" style="width: 100%" value="<?=$ctHandle?>" /></td>
	</tr>
	<tr>
		<td colspan="3" class="subheader">Icon</td>
	</tr>
	<tr>
		<td colspan="3">
		<? 
		$first = true;
		foreach($icons as $ic) { ?>
			<?
			$checked = false;
			if ($ct->getCollectionTypeIcon() == $ic || $first) { 
				$checked = 'checked';
			}
			$first = false;
			?>
			<span style="white-space: nowrap; margin-right: 20px">
			<input type="radio" name="ctIcon" value="<?=$ic?>" style="vertical-align: middle" <?=$checked?> />
			<img src="<?=REL_DIR_FILES_COLLECTION_TYPE_ICONS?>/<?=$ic?>" style="vertical-align: middle" /></span>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="header">Available Metadata Attributes</td>
	</tr>
	<?
		$attribs = CollectionAttributeKey::getList();
		$i = 0;
		foreach($attribs as $ak) { 
		if ($i == 0) { ?>
			<tr>
		<? } ?>
		
		<td><input type="checkbox" name="akID[]" value="<?=$ak->getCollectionAttributeKeyID()?>" <? if (($this->controller->isPost() && in_array($ak->getCollectionAttributeKeyID(), $_POST['akID']))) { ?> checked <? } else if ((!$this->controller->isPost()) && $ct->isAvailableCollectionTypeAttribute($ak->getCollectionAttributeKeyID())) { ?> checked <? } ?> /> <?=$ak->getCollectionAttributeKeyName()?></td>
		
		<? $i++;
		
		if ($i == 3) { ?>
		</tr>
		<? 
		$i = 0;
		}
		
	}
	
	if ($i < 3 && $i > 0) {
		for ($j = $i; $j < 3; $j++) { ?>
			<td>&nbsp;</td>
		<? }
	?></tr>
	<? } ?>
	<tr>
		<td colspan="3" class="header"><input type="submit" value="Update Page Type" /> <input type="button" name="cancel" value="Cancel" onclick="location.href='<?=$this->url('/dashboard/collection_types/')?>'" /></td>
	</tr>
    <tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td colspan="3" class="header"><input type="button" name="delete" value="Delete Page Type" onclick="if(confirm('Are you sure?')){ location.href='<?=$this->url('/dashboard/collection_types/','delete',$_REQUEST['ctID'])?>';}" /></td>
	</tr>
    </table>
	</div>
	
	<br>
	</form>	
	</div>
	
<? 
} else if ($_REQUEST['task'] == 'add') {  ?>
	
	<h1><span>Add Page Type (<em class="required">*</em> - required field)</span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<form method="post" action="<?=$this->url('/dashboard/collection_types/')?>">
	<input type="hidden" name="task" value="add" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" colspan="2">Name <span class="required">*</span></td>
		<td class="subheader">Handle <span class="required">*</span></td>
	</tr>	
	<tr>
		<td style="width: 65%"  colspan="2"><input type="text" name="ctName" style="width: 100%" value="<?=$_POST['ctName']?>" /></td>
		<td style="width: 35%"><input type="text" name="ctHandle" style="width: 100%" value="<?=$_POST['ctHandle']?>" /></td>
	</tr>
	<tr>
		<td colspan="3" class="subheader">Icon</td>
	</tr>
	<tr>
		<td colspan="3">
		<? 
		$first = true;
		foreach($icons as $ic) { ?>
			<?
			$checked = false;
			if ($first) { 
				$checked = 'checked';
			}
			$first = false;
			?>
			<span style="white-space: nowrap; margin-right: 20px">
			<input type="radio" name="ctIcon" value="<?=$ic?>" style="vertical-align: middle" <?=$checked?> />
			<img src="<?=REL_DIR_FILES_COLLECTION_TYPE_ICONS?>/<?=$ic?>" style="vertical-align: middle" /></span>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td colspan="3">Available Metadata Attributes</td>
	</tr>
	<?
		$attribs = CollectionAttributeKey::getList();
		$i = 0;
		foreach($attribs as $ak) { 
		if ($i == 0) { ?>
			<tr>
		<? } ?>
		
		<td><input type="checkbox" name="akID[]" value="<?=$ak->getCollectionAttributeKeyID()?>" /> <?=$ak->getCollectionAttributeKeyName()?></td>
		
		<? $i++;
		
		if ($i == 3) { ?>
		</tr>
		<? 
		$i = 0;
		}
		
	}
	
	if ($i < 3 && $i > 0) {
		for ($j = $i; $j < 3; $j++) { ?>
			<td>&nbsp;</td>
		<? }
	?></tr>
	<? } ?>
	<tr>
		<td colspan="3" class="header"><input type="submit" name="add" value="Add Page Type" /> <input type="button" name="cancel" value="Cancel" onclick="location.href='<?=$this->url('/dashboard/collection_types/')?>'" />
	</tr>
	</table>
	</div>
	
	<br>
	</form>	
	</div>

<?
} else { ?>

	<h1><span>Page Types</span></h1>
	<div class="ccm-dashboard-inner">
	

	<? if (count($ctArray) == 0) { ?>
		<br/><strong>No page types found.</strong><br/><br>
	<? } else { ?>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list" width="600">
	<tr>
		<td class="subheader" width="100%">Name</td>
		<td class="subheader">Handle</td>
		<td class="subheader">Package</td>
		<td class="subheader" colspan="2">Edit</td>
	</tr>
	<? foreach ($ctArray as $ct) { ?>
	<tr>
		<td><?=$ct->getCollectionTypeName()?></td>
		<td><?=$ct->getCollectionTypeHandle()?></td>
		<td><?
			if ($ct->getPackageID() > 0) {
				$package = Package::getByID($ct->getPackageID());
				print $package->getPackageName(); 
			} else {
				print '(None)';
			}
			?></td>
		<td>
		<? if ($ct->getMasterCollectionID()) {?>
			<? print $ih->button_js('Defaults', "window.open('" . $this->url('/dashboard/collection_types?cID=' . $ct->getMasterCollectionID() . '&task=load_master')."')", 'left', false, array('title'=>'Lets you set default permissions and blocks for a particular page type.'));?>
		<? } ?>
	
		</td>
		<td><? print $ih->button('Edit', $this->url('/dashboard/collection_types?ctID=' . $ct->getCollectionTypeID() . '&task=edit'))?></td>

	</tr>
	<? } ?>
	
	</table>
	</div>
	
	<? } ?>
	
	<br/>
	<div class="ccm-buttons">
		<a class="ccm-button" href="<?=$this->url('/dashboard/collection_types?task=add')?>"><span><em class="ccm-button-add">Add a Page Type</em></span></a>	
	</div>
	<div class="ccm-spacer">&nbsp;</div>

	</div>
	
	
	<h1><span>Page Attributes</span></h1>
	<div class="ccm-dashboard-inner">
	
	<? if (count($attribs) > 0) { ?>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<td class="subheader" width="100%">Name</td>
		<td class="subheader">Handle</td>
		<td class="subheader">&nbsp;</td>
		<td class="subheader">&nbsp;</td>
	</tr>
	<?
	foreach($attribs as $ak) { ?>
	<tr>
		<td><?=$ak->getCollectionAttributeKeyName()?></td>
		<td style="white-space: nowrap"><?=$ak->getCollectionAttributeKeyHandle()?></td>
		<td><? print $ih->button('Edit', $this->url('/dashboard/collection_types/attributes?akID=' . $ak->getCollectionAttributeKeyID() . '&task=edit'))?></td>
		<td><? print $ih->button('Delete', "javascript:if (confirm('Are you sure you wish to delete this attribute?')) { location.href='" . $this->url('/dashboard/collection_types/attributes?akID=' . $ak->getCollectionAttributeKeyID() . '&task=delete') . "' }")?></td>
	</tr>
	<? } ?>
	</table>
	</div>
	
	<? } else { ?>
		
	<br/><strong>No page attributes defined.</strong><br/><br/>
		
	<? } ?>
	
	<br/>
	<div class="ccm-buttons">
		<a class="ccm-button" href="<?=$this->url('/dashboard/collection_types/attributes')?>"><span>Add Page Attribute</span></a>	
	</div>
	<div class="ccm-spacer">&nbsp;</div>

	</div>
	
	

	<h1><span>Single Pages</span></h1>
	<div class="ccm-dashboard-inner">
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list" width="600">
	<tr>
		<td colspan="4" class="header">Already Installed</td>
	</tr>
	<tr>
		<td class="subheader" width="100%">Name</td>
		<td class="subheader">Path</td>
		<td class="subheader">Package</td>
		<td class="subheader">&nbsp;</td>
	</tr>
	<? if (count($generated) == 0) { ?>
		<td colspan="4">No pages found.</td>
	<? } else { ?>
	
	<? foreach ($generated as $p) { ?>
	<?
		if ($p->getPackageID() > 0) {
			$package = Package::getByID($p->getPackageID());
			$packageHandle = $package->getPackageHandle();
			$packageName = $package->getPackageName();
		} else {
			$packageName = '(None)';
		}
		
	?>
	<tr <? if ($packageHandle == DIRNAME_PACKAGE_CORE) { ?> class="ccm-core-package-row" <? } ?>>
		<td><a href="<?=DIR_REL?>/index.php?cID=<?=$p->getCollectionID()?>"><?=$p->getCollectionName()?></a></td>
		<td><?=$p->getCollectionPath()?></td>
		<td><? print $packageName; ?></td>
		<td>
			<? print $ih->button('Refresh',$this->url('/dashboard/collection_types/?p=' . $p->getCollectionID() . '&task=refresh'), 'left', false, array('title'=>'Regenerates the page title and permissions based on their filesystem settings.'));?>
		</td>
	</tr>
	<? }
	
	} ?>
	<tr>
		<td colspan="4" class="header">Add Single Page</td>
	</tr>
	<tr>
		<td colspan="4">The page you want to add is available at:
		<br>
		<form method="post" id="add_static_page_form" action="<?=$this->url('/dashboard/collection_types/')?>">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td>
		<?=BASE_URL?>/<input type="text" name="pageURL" value="<?=$_POST['pageURL']?>" style="width: 200px" /></td>
		<td>
		<? print $ih->submit('Add', 'add_static_page_form', 'left');?></td>
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

<? }