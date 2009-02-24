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
	exit;
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
				$this->controller->redirect('/dashboard/pages/types?created=1');
			} else if (is_object($ct)) {
				$ct->update($_POST);
				$this->controller->redirect('/dashboard/pages/types?updated=1');
			}		
			exit;
		} catch(Exception $e1) {
			$error[] = $e1->getMessage();
		}
	}
}

if ($_REQUEST['created']) { 
	$message = t('Page Type added.');
} else if ($_REQUEST['updated']) {
	$message = t('Page Type updated.');
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
	
	<form method="post" id="update_page_type" action="<?=$this->url('/dashboard/pages/types/')?>">
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
		<? print $ih->button(t('Cancel'), $this->url('/dashboard/pages/types'), 'left');?>
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
	
	<? print $ih->button_js(t('Delete Page Type'), "deletePageType()", 'left');?>
	
	<div class="ccm-spacer">&nbsp;</div>
	<?
	$confirmMsg = t('Are you sure?');
	?>
	<script type="text/javascript">
	deletePageType = function() {
		if(confirm('<?=$confirmMsg?>')){ 
			location.href="<?=$this->url('/dashboard/pages/types/','delete',$_REQUEST['ctID'], $valt->generate('delete_page_type'))?>";
		}	
	}
	</script>
	</div>
	
<? 
} else if ($_REQUEST['task'] == 'add') {  ?>
	
	<h1><span><?=t('Add Page Type')?> (<em class="required">*</em> - <?=t('required field')?>)</span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<form method="post" id="add_page_type" action="<?=$this->url('/dashboard/pages/types/')?>">
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
		<? print $ih->button(t('Cancel'), $this->url('/dashboard/pages/types'), 'left');?>
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
			$package = false;
			if ($ct->getPackageID() > 0) {
				$package = Package::getByID($ct->getPackageID());
			}
			if (is_object($package)) {
				print $package->getPackageName(); 
			} else {
				print t('None');
			}
			?></td>
		<td>
		<? if ($ct->getMasterCollectionID()) {?>
			<? if ($u->getUserID() == USER_SUPER_ID) { ?>
				<? print $ih->button_js(t('Defaults'), "window.open('" . $this->url('/dashboard/pages/types?cID=' . $ct->getMasterCollectionID() . '&task=load_master')."')", 'left', false, array('title'=>t('Lets you set default permissions and blocks for a particular page type.')) );?>
			<? } else { 
				$defaultsErrMsg = t('You must be logged in as %s to edit default content on page types.', USER_SUPER);
				?>
				<? print $ih->button_js(t('Defaults'), "alert('" . $defaultsErrMsg . "')", 'left', false, array('title'=>t('Lets you set default permissions and blocks for a particular page type.')) );?>
			<? } ?>
		<? } ?>
	
		</td>
		<td><? print $ih->button(t('Edit'), $this->url('/dashboard/pages/types?ctID=' . $ct->getCollectionTypeID() . '&task=edit'))?></td>

	</tr>
	<? } ?>
	
	</table>
	</div>
	
	<? } ?>
	
	<br/>
	<div class="ccm-buttons">
		<a class="ccm-button" href="<?=$this->url('/dashboard/pages/types?task=add')?>"><span><em class="ccm-button-add"><?=t('Add a Page Type')?></em></span></a>	
	</div>
	<div class="ccm-spacer">&nbsp;</div>

	</div>
	
	
	<h1><span><?=t('Page Attributes')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<?= Loader::element('dashboard/attributes_table', array('attribs'=>$attribs) ); ?>
	
	<br/>
	<div class="ccm-buttons">
		<a class="ccm-button" href="<?=$this->url('/dashboard/pages/types/attributes')?>"><span><?=t('Add Page Attribute')?></span></a>	
	</div>
	<div class="ccm-spacer">&nbsp;</div>

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