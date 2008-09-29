<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('collection_attributes');
Loader::model('collection_types');

$section = 'collection_types';

if ($_REQUEST['task'] == 'edit') {
	$ak = CollectionAttributeKey::get($_REQUEST['akID']);
	if (is_object($ak)) { 		
		if ($_POST['update']) {
		
			$akType = $_POST['akType'];
			$akName = $_POST['akName'];
			$akHandle = $_POST['akHandle'];
			$akSearchable = $_POST['akSearchable'];
			$akValues = $_POST['akValues'];
			
		} else {
			
			$akType = $ak->getCollectionAttributeKeyType();
			$akName = $ak->getCollectionAttributeKeyName();
			$akHandle = $ak->getCollectionAttributeKeyHandle();
			$akSearchable = $ak->isCollectionAttributeKeySearchable();
			$akValues = $ak->getCollectionAttributeKeyValues();
		
		}
		
		$editMode = true;
	}
}

$txt = Loader::helper('text');

if ($_POST['add'] || $_POST['update']) {
	$akHandle = $txt->sanitize($_POST['akHandle']);
	$akName = $txt->sanitize($_POST['akName']);
	$akValues = $txt->sanitize($_POST['akValues']);
	$akType = $txt->sanitize($_POST['akType']);
	$akSearchable = $_POST['akSearchable'] ? 1 : 0;
	
	$error = array();
	if (!$akHandle) {
		$error[] = "Handle required.";
	}
	if (!$akName) {
		$error[] = "Name required.";
	}
	if (!$akType) {
		$error[] = "Type required.";
	}
	if ($akType == 'SELECT' && !$akValues) {
		$error[] = "A select attribute must have at least one option.";
	}
	
	if (count($error) == 0) {
		if ($_POST['add']) {
			if ($akHandle) {
				if (CollectionAttributeKey::inUse($akHandle)) {
					$error[] = "An attribute with the handle '" . $akHandle . "' already exists.";
				}
			}
			if (count($error) == 0) {
				$ck = CollectionAttributeKey::add($akHandle, $akName, $akSearchable, $akValues, $akType);
				$this->controller->redirect('/dashboard/collection_types/?attribute_created=1');
			}
		} else if (is_object($ak)) {
			$ak = $ak->update($akHandle, $akName, $akSearchable, $akValues, $akType);
			$this->controller->redirect('/dashboard/collection_types/?attribute_updated=1');
		}		
		exit;
	}
}

if ($_REQUEST['task'] == 'delete') { 
	$ck = CollectionAttributeKey::get($_REQUEST['akID']);
	if (is_object($ck)) {
		$ck->delete();
		$this->controller->redirect('/dashboard/collection_types/?attribute_deleted=1');
	}
}

if ($_GET['created']) {
	$message = "Attribute Key Created.";
} else if ($_GET['deleted']) { 
	$message = "Attribute Key Deleted.";
} else if ($_GET['updated']) {
	$message = "Attribute Key Updated.";
}

$attribs = CollectionAttributeKey::getList();


if ($editMode) { ?>	

<h1><span>Edit Attribute Definition (<em class="required">*</em> - required field)</span></h1>
<div class="ccm-dashboard-inner">
	<form method="post" id="ccm-attribute-update" action="<?=$this->url('/dashboard/collection_types/attributes/')?>">
	<input type="hidden" name="akID" value="<?=$_REQUEST['akID']?>" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="update" value="1" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader">Handle <span class="required">*</span></td>
		<td class="subheader">Type <span class="required">*</span></td>
		<td class="subheader">Searchable? <span class="required">*</span></td>
	</tr>	
	<tr>
		<td style="width: 33%"><input type="text" name="akHandle" style="width: 100%" value="<?=$akHandle?>" /></td>
		<td style="width: 33%"><select name="akType" style="width: 100%" onchange="if (this.value == 'SELECT') { document.getElementById('akValues').disabled = false; document.getElementById('reqValues').style.display='inline'; } else {  document.getElementById('reqValues').style.display='none'; document.getElementById('akValues').disabled = true; }">
			<option value="TEXT"<? if ($akType == 'TEXT') { ?> selected<? } ?>>Text Box</option>
			<option value="BOOLEAN"<? if ($akType == 'BOOLEAN') { ?> selected<? } ?>>Check Box</option>
			<option value="SELECT"<? if ($akType == 'SELECT') { ?> selected<? } ?>>Select Menu</option>
			<option value="SELECT_ADD"<? if ($akType == 'SELECT_ADD') { ?> selected<? } ?>>Select Menu + Add Option</option>
			<option value="DATE"<? if ($akType == 'DATE') { ?> selected <? } ?>>Date</option>
		</select></td>
		<td style="width: 33%"><input type="checkbox" name="akSearchable" style="vertical-align: middle" <? if ($akSearchable) { ?> checked <? } ?> /> Yes, include this field in the search index.</td>
	</tr>
	<tr>
		<td class="subheader" colspan="3">Name <span class="required">*</span></td>
	</tr>
	<tr>
		<td colspan="3"><input type="text" name="akName" style="width: 100%" value="<?=$akName?>" /></td>
	</tr>
	<tr>
		<td class="subheader" colspan="3">Values <span class="required" id="reqValues" <? if ($akType != 'SELECT') { ?> style="display: none"<? } ?>>*</span></td>
	</tr>
	<tr>
		<td colspan="3"><input type="text" id="akValues" name="akValues" style="width: 100%" value="<?=$akValues?>" <? if ($akType != 'SELECT') { ?> disabled <? } ?> /><br/>(For select types only - separate menu options with a comma, no space.)</td>
	</tr>
	<tr>
		<td colspan="3" class="header">

		<a href="<?=$this->url('/dashboard/collection_types')?>" class="ccm-button-left"><span>Cancel</span></a>
		<a href="javascript:void(0)" onclick="$('#ccm-attribute-update').get(0).submit()" class="ccm-button-right"><span>Update</span></a>
		
		</td>
	</tr>
	</table>
	</div>
	
	<br>
	</form>	
	
</div>

<? 

} else { ?>

<h1><span>Add Page Attribute</span></h1>
<div class="ccm-dashboard-inner">

<form method="post" id="ccm-add-attribute" action="<?=$this->url('/dashboard/collection_types/attributes/')?>">
<input type="hidden" name="add" value="1" />

<div style="margin:0px; padding:0px; width:100%; height:auto" >	
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader">Handle <span class="required">*</span></td>
	<td class="subheader">Type <span class="required">*</span></td>
	<td class="subheader">Searchable? <span class="required">*</span></td>
</tr>	
<tr>
	<td style="width: 33%"><input type="text" name="akHandle" style="width: 100%" value="<?=$_POST['akHandle']?>" /></td>
	<td style="width: 33%"><select name="akType" style="width: 100%" onchange="if (this.value == 'SELECT') { document.getElementById('akValues').disabled = false; document.getElementById('reqValues').style.display='inline'; } else {  document.getElementById('reqValues').style.display='none'; document.getElementById('akValues').disabled = true; }">
		<option value="TEXT"<? if ($_POST['akType'] == 'TEXT') { ?> selected<? } ?>>Text Box</option>
		<option value="BOOLEAN"<? if ($_POST['akType'] == 'BOOLEAN') { ?> selected<? } ?>>Check Box</option>
		<option value="SELECT"<? if ($_POST['akType'] == 'SELECT') { ?> selected<? } ?>>Select Menu</option>
		<option value="SELECT_ADD"<? if ($_POST['akType'] == 'SELECT_ADD') { ?> selected<? } ?>>Select Menu + Add Option</option>
		<option value="DATE"<? if ($_POST['akType'] == 'DATE') { ?> selected <? } ?>>Date</option>
	</select></td>
	<td style="width: 33%"><input type="checkbox" name="akSearchable" style="vertical-align: middle" <? if ($_POST['akSearchable']) { ?> checked <? } ?> /> Yes, include this field in the search index.</td>
</tr>
<tr>
	<td class="subheader" colspan="3">Name <span class="required">*</span></td>
</tr>
<tr>
	<td colspan="3"><input type="text" name="akName" style="width: 100%" value="<?=$_POST['akName']?>" /></td>
</tr>
<tr>
	<td class="subheader" colspan="3">Values <span class="required" id="reqValues" <? if ($_POST['akType'] != 'SELECT') { ?> style="display: none"<? } ?>>*</span></td>
</tr>
<tr>
	<td colspan="3"><input type="text" id="akValues" name="akValues" style="width: 100%" value="<?=$_POST['akValues']?>" <? if ($_POST['akType'] != 'SELECT') { ?> disabled <? } ?> /><br/>(For select types only - separate menu options with a comma, no space.)</td>
</tr>
<tr>
	<td colspan="3" class="header">

	<a href="<?=$this->url('/dashboard/collection_types')?>" class="ccm-button-left"><span>Cancel</span></a>
	<a href="javascript:void(0)" onclick="$('#ccm-add-attribute').get(0).submit()" class="ccm-button-right"><span>Add Attribute</span></a>
	
	</td>
</tr>
</table>
</div>

<br>
</form>	
</div>


<? } ?>