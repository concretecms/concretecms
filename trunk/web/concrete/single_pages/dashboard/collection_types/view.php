<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('collection_types');
Loader::model('single_page');
Loader::model('collection_attributes');
$attribs = CollectionAttributeKey::getList();
$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');

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
	
	if (!$valt->validate('add_or_update_page_type')) {
		$error[] = $valt->getErrorMessage();
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
	if ($_REQUEST['p'] && $_REQUEST['task'] == 'refresh' && $valt->validate('refresh')) { 
		$p = SinglePage::getByID($_REQUEST['p']);
		$p->refresh();
		$this->controller->redirect('/dashboard/collection_types?refreshed=1');
		exit;
	}
	
	if ($_POST['add_static_page']) {
		if ($valt->validate("add_single_page")) {
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
		} else {
			$error[] = $valt->getErrorMessage();
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

<?
if ($ctEditMode) { 
	$ct->populateAvailableAttributeKeys();
	?>	

	<h1><span><?=t('Edit Page Type')?> (<em class="required">*</em> - <?=t('required field')?>)</span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<form method="post" id="update_page_type" action="<?=$this->url('/dashboard/collection_types/')?>">
	<?=$valt->output('add_or_update_page_type')?>
	<input type="hidden" name="ctID" value="<?=$_REQUEST['ctID']?>" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="update" value="1" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" colspan="2"><?=t('Name')?> <span class="required">*</span></td>
		<td class="subheader"><?=t('Handle')?> <span class="required">*</span></td>
	</tr>
	<tr>
		<td style="width: 65%" colspan="2"><input type="text" name="ctName" style="width: 100%" value="<?=$ctName?>" /></td>
		<td style="width: 35%"><input type="text" name="ctHandle" style="width: 100%" value="<?=$ctHandle?>" /></td>
	</tr>
	<tr>
		<td colspan="3" class="subheader"><?=t('Icon')?></td>
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
		<td colspan="3" class="header"><?=t('Available Metadata Attributes')?></td>
	</tr>
	<?
		$attribs = CollectionAttributeKey::getList();
		$i = 0;
		foreach($attribs as $ak) { 
		if ($i == 0) { ?>
			<tr>
		<? } ?>
		
		<td><input type="checkbox" name="akID[]" value="<?=$ak->getCollectionAttributeKeyID()?>" <? if (($this->controller->isPost() && in_array($ak->getCollectionAttributeKeyID(), $akIDArray))) { ?> checked <? } else if ((!$this->controller->isPost()) && $ct->isAvailableCollectionTypeAttribute($ak->getCollectionAttributeKeyID())) { ?> checked <? } ?> /> <?=$ak->getCollectionAttributeKeyName()?></td>
		
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
		<td colspan="3" class="header">
		<? print $ih->submit(t('Update Page Type'), 'update_page_type', 'right');?>
		<? print $ih->button(t('Cancel'), $this->url('/dashboard/collection_types'), 'left');?>
		</td>
	</tr>
    </table>
	</div>
	
	<br>
	</form>	
	</div>
	
	<h1><span><?=t('Delete Page Type')?></span></h1>
	<div class="ccm-dashboard-inner">


	<p><?=t('Click below to remove this page type entirely. (Note: You may only remove page types which are not being used on your site. If a page type is being used, delete all instances of its pages first.)')?> 
	<div class="ccm-spacer">&nbsp;</div>
	
	<? print $ih->button_js(t('Delete Page Type'), "deletePageType", 'left');?>
	
	<div class="ccm-spacer">&nbsp;</div>
	<?
	$confirmMsg = t('Are you sure?');
	?>
	<script type="text/javascript">
	deletePageType = function() {
		if(confirm('<?=$confirmMsg?>')){ 
			location.href="<?=$this->url('/dashboard/collection_types/','delete',$_REQUEST['ctID'], $valt->generate('delete_page_type'))?>";
		}	
	}
	</script>
	</div>
	
<? 
} else if ($_REQUEST['task'] == 'add') {  ?>
	
	<h1><span><?=t('Add Page Type')?> (<em class="required">*</em> - <?=t('required field')?>)</span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<form method="post" id="add_page_type" action="<?=$this->url('/dashboard/collection_types/')?>">
	<?=$valt->output('add_or_update_page_type')?>
	<input type="hidden" name="task" value="add" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" colspan="2"><?=t('Name')?> <span class="required">*</span></td>
		<td class="subheader"><?=t('Handle')?> <span class="required">*</span></td>
	</tr>	
	<tr>
		<td style="width: 65%"  colspan="2"><input type="text" name="ctName" style="width: 100%" value="<?=$_POST['ctName']?>" /></td>
		<td style="width: 35%"><input type="text" name="ctHandle" style="width: 100%" value="<?=$_POST['ctHandle']?>" /></td>
	</tr>
	<tr>
		<td colspan="3" class="subheader"><?=t('Icon')?></td>
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
		<td colspan="3"><?=t('Available Metadata Attributes')?></td>
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
		<td colspan="3" class="header">
		<? print $ih->submit(t('Add Page Type'), 'add_page_type', 'right');?>
		<? print $ih->button(t('Cancel'), $this->url('/dashboard/collection_types'), 'left');?>
		</td>
	</tr>
	</table>
	</div>
	
	<br>
	</form>	
	</div>

<?
} else { ?>

	<h1><span><?=t('Page Types')?></span></h1>
	<div class="ccm-dashboard-inner">
	

	<? if (count($ctArray) == 0) { ?>
		<br/><strong><?=t('No page types found.')?></strong><br/><br>
	<? } else { ?>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list" width="600">
	<tr>
		<td class="subheader" width="100%"><?=t('Name')?></td>
		<td class="subheader"><?=t('Handle')?></td>
		<td class="subheader"><?=t('Package')?></td>
		<td class="subheader"><div style="width: 90px"></div></td>
		<td class="subheader"><div style="width: 60px"></div></td>
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
				print t('None');
			}
			?></td>
		<td>
		<? if ($ct->getMasterCollectionID()) {?>
			<? print $ih->button_js(t('Defaults'), "window.open('" . $this->url('/dashboard/collection_types?cID=' . $ct->getMasterCollectionID() . '&task=load_master')."')", 'left', false, array('title'=>t('Lets you set default permissions and blocks for a particular page type.')) );?>
		<? } ?>
	
		</td>
		<td><? print $ih->button(t('Edit'), $this->url('/dashboard/collection_types?ctID=' . $ct->getCollectionTypeID() . '&task=edit'))?></td>

	</tr>
	<? } ?>
	
	</table>
	</div>
	
	<? } ?>
	
	<br/>
	<div class="ccm-buttons">
		<a class="ccm-button" href="<?=$this->url('/dashboard/collection_types?task=add')?>"><span><em class="ccm-button-add"><?=t('Add a Page Type')?></em></span></a>	
	</div>
	<div class="ccm-spacer">&nbsp;</div>

	</div>
	
	
	<h1><span><?=t('Page Attributes')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<? if (count($attribs) > 0) { ?>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<td class="subheader" width="100%"><?=t('Name')?></td>
		<td class="subheader"><?=t('Handle')?></td>
		<td class="subheader"><div style="width: 60px"></div></td>
		<td class="subheader"><div style="width: 70px"></div></td>
	</tr>
	<?
	foreach($attribs as $ak) { ?>
	<tr>
		<td><?=$ak->getCollectionAttributeKeyName()?></td>
		<td style="white-space: nowrap"><?=$ak->getCollectionAttributeKeyHandle()?></td>
		<td><? print $ih->button(t('Edit'), $this->url('/dashboard/collection_types/attributes?akID=' . $ak->getCollectionAttributeKeyID() . '&task=edit'))?></td>
		<td><? print $ih->button(t('Delete'), "javascript:if (confirm('".t('Are you sure you wish to delete this attribute?')."')) { location.href='" . $this->url('/dashboard/collection_types/attributes?akID=' . $ak->getCollectionAttributeKeyID() . '&task=delete&' . $valt->getParameter('delete_attribute')) . "' }")?></td>
	</tr>
	<? } ?>
	</table>
	</div>
	
	<? } else { ?>
		
	<br/><strong><?=t('No page attributes defined.')?></strong><br/><br/>
		
	<? } ?>
	
	<br/>
	<div class="ccm-buttons">
		<a class="ccm-button" href="<?=$this->url('/dashboard/collection_types/attributes')?>"><span><?=t('Add Page Attribute')?></span></a>	
	</div>
	<div class="ccm-spacer">&nbsp;</div>

	</div>
	
	

	<h1><span><?=t('Single Pages')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list" width="600">
	<tr>
		<td colspan="4" class="header"><?=t('Already Installed')?></td>
	</tr>
	<tr>
		<td class="subheader" width="100%"><?=t('Name')?></td>
		<td class="subheader"><?=t('Path')?></td>
		<td class="subheader"><?=t('Package')?></td>
		<td class="subheader"><div style="width: 90px"></div></td>
	</tr>
	<? if (count($generated) == 0) { ?>
		<td colspan="4"><?=t('No pages found.')?></td>
	<? } else { ?>
	
	<? foreach ($generated as $p) { ?>
	<?
		if ($p->getPackageID() > 0) {
			$package = Package::getByID($p->getPackageID());
			$packageHandle = $package->getPackageHandle();
			$packageName = $package->getPackageName();
		} else {
			$packageName = t('None');
		}
		
	?>
	<tr <? if ($packageHandle == DIRNAME_PACKAGE_CORE) { ?> class="ccm-core-package-row" <? } ?>>
		<td><a href="<?=DIR_REL?>/index.php?cID=<?=$p->getCollectionID()?>"><?=$p->getCollectionName()?></a></td>
		<td><?=$p->getCollectionPath()?></td>
		<td><? print $packageName; ?></td>
		<td>
			<? print $ih->button(t('Refresh'),$this->url('/dashboard/collection_types/?p=' . $p->getCollectionID() . '&task=refresh&' . $valt->getParameter('refresh')), 'left', false, array('title'=>t('Refreshes the page, rebuilding its permissions and its name.')));?>
		</td>
	</tr>
	<? }
	
	} ?>
	<tr>
		<td colspan="4" class="header"><?=t('Add Single Page')?></td>
	</tr>
	<tr>
		<td colspan="4"><?=t('The page you want to add is available at:')?>
		<br>
		<form method="post" id="add_static_page_form" action="<?=$this->url('/dashboard/collection_types/')?>">
		<?=$valt->output('add_single_page')?>
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td>
		<?=BASE_URL?>/<input type="text" name="pageURL" value="<?=$_POST['pageURL']?>" style="width: 200px" /></td>
		<td>
		<? print $ih->submit(t('Add'), 'add_static_page_form', 'left');?></td>
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