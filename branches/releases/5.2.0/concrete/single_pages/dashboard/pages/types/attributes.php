<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('collection_attributes');
Loader::model('collection_types');

$section = 'collection_types';
$valt = Loader::helper('validation/token');

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
	$akValues = preg_replace('/\r\n|\r/', "\n", $akValues); // make linebreaks consistant
	$akType = $txt->sanitize($_POST['akType']);
	$akSearchable = $_POST['akSearchable'] ? 1 : 0;
	
	$error = array();
	if (!$akHandle) {
		$error[] = t("Handle required.");
	}
	if (!$akName) {
		$error[] = t("Name required.");
	}
	if (!$akType) {
		$error[] = t("Type required.");
	}
	if ($akType == 'SELECT' && !$akValues) {
		$error[] = t("A select attribute must have at least one option.");
	}
	
	if (!$valt->validate('add_or_update_attribute')) {
		$error[] = $valt->getErrorMessage();
	}
	
	if (count($error) == 0) {
		if ($_POST['add']) {
			if ($akHandle) {
				if (CollectionAttributeKey::inUse($akHandle)) {
					$error[] = t("An attribute with the handle %s already exists.", $akHandle);
				}
			}
			if (count($error) == 0) {
				$ck = CollectionAttributeKey::add($akHandle, $akName, $akSearchable, $akValues, $akType);
				$this->controller->redirect('/dashboard/pages/types/?attribute_created=1');
			}
		} else if (is_object($ak)) {
			$ak = $ak->update($akHandle, $akName, $akSearchable, $akValues, $akType);
			$this->controller->redirect('/dashboard/pages/types/?attribute_updated=1');
		}		
		exit;
	}
}

if ($_REQUEST['task'] == 'delete' && $valt->validate('delete_attribute')) { 
	$ck = CollectionAttributeKey::get($_REQUEST['akID']);
	if (is_object($ck)) {
		$ck->delete();
		$this->controller->redirect('/dashboard/pages/types/?attribute_deleted=1');
	}
}

if ($_GET['created']) {
	$message = t("Attribute Key Created.");
} else if ($_GET['deleted']) { 
	$message = t("Attribute Key Deleted.");
} else if ($_GET['updated']) {
	$message = t("Attribute Key Updated.");
}

$attribs = CollectionAttributeKey::getList();


if ($editMode) { ?>	

<h1><span><?php echo t('Edit Attribute Definition')?> (<em class="required">*</em> - <?php echo t('required field')?>)</span></h1>
<div class="ccm-dashboard-inner">
	<form method="post" id="ccm-attribute-update" action="<?php echo $this->url('/dashboard/pages/types/attributes/')?>">
	<?php echo $valt->output('add_or_update_attribute')?>
	<input type="hidden" name="akID" value="<?php echo $_REQUEST['akID']?>" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="update" value="1" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader"><?php echo t('Handle')?> <span class="required">*</span></td>
		<td class="subheader"><?php echo t('Type')?> <span class="required">*</span></td>
		<td class="subheader"><?php echo t('Searchable?')?> <span class="required">*</span></td>
	</tr>	
	<tr>
		<td style="width: 33%"><input type="text" name="akHandle" style="width: 100%" value="<?php echo $akHandle?>" /></td>
		<td style="width: 33%"><select name="akType" style="width: 100%" onchange="if (this.value == 'SELECT') { document.getElementById('akValues').disabled = false; document.getElementById('reqValues').style.display='inline'; } else {  document.getElementById('reqValues').style.display='none'; document.getElementById('akValues').disabled = true; }">
			<option value="TEXT"<?php  if ($akType == 'TEXT') { ?> selected<?php  } ?>><?php echo t('Text Box')?></option>
			<option value="BOOLEAN"<?php  if ($akType == 'BOOLEAN') { ?> selected<?php  } ?>><?php echo t('Check Box')?></option>
			<option value="SELECT"<?php  if ($akType == 'SELECT') { ?> selected<?php  } ?>><?php echo t('Select Menu')?></option>
			<option value="SELECT_ADD"<?php  if ($akType == 'SELECT_ADD') { ?> selected<?php  } ?>><?php echo t('Select Menu + Add Option')?></option>
			<option value="DATE"<?php  if ($akType == 'DATE') { ?> selected <?php  } ?>><?php echo t('Date')?></option>
			<option value="IMAGE_FILE"<?php  if ($akType == 'IMAGE_FILE') { ?> selected <?php  } ?>><?php echo t('Image/File')?></option>
		</select></td>
		<td style="width: 33%"><input type="checkbox" name="akSearchable" style="vertical-align: middle" <?php  if ($akSearchable) { ?> checked <?php  } ?> /> <?php echo t('Yes, include this field in the search index.')?></td>
	</tr>
	<tr>
		<td class="subheader" colspan="3"><?php echo t('Name')?> <span class="required">*</span></td>
	</tr>
	<tr>
		<td colspan="3"><input type="text" name="akName" style="width: 100%" value="<?php echo $akName?>" /></td>
	</tr>
	<tr>
		<td class="subheader" colspan="3"><?php echo t('Values')?> <span class="required" id="reqValues" <?php  if ($akType != 'SELECT') { ?> style="display: none"<?php  } ?>>*</span></td>
	</tr>
	<tr>
		<td colspan="3">
        <textarea id="akValues" name="akValues" style="width: 100%" <?php  if ($akType != 'SELECT') { ?> disabled="disabled" <?php  } ?>><?php echo $akValues?></textarea>
        <br/>(<?php echo t('For select types only - separate menu options with line breaks')?>)
        <!-- 
        <input type="text" id="akValues" name="akValues" style="width: 100%" value="<?php echo $akValues?>" <?php  if ($akType != 'SELECT') { ?> disabled <?php  } ?> /><br/>(<?php echo t('For select types only - separate menu options with a comma, no space.')?>)
        	-->
        </td>
	</tr>
	<tr>
		<td colspan="3" class="header">

		<a href="<?php echo $this->url('/dashboard/pages/types')?>" class="ccm-button-left"><span><?php echo t('Cancel')?></span></a>
		<a href="javascript:void(0)" onclick="$('#ccm-attribute-update').get(0).submit()" class="ccm-button-right"><span><?php echo t('Update')?></span></a>
		
		</td>
	</tr>
	</table>
	</div>
	
	<br>
	</form>	
	
</div>

<?php  

} else { ?>

<h1><span><?php echo t('Add Page Attribute')?></span></h1>
<div class="ccm-dashboard-inner">

<form method="post" id="ccm-add-attribute" action="<?php echo $this->url('/dashboard/pages/types/attributes/')?>">
<input type="hidden" name="add" value="1" />
<?php echo $valt->output('add_or_update_attribute')?>

<div style="margin:0px; padding:0px; width:100%; height:auto" >	
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?php echo t('Handle')?> <span class="required">*</span></td>
	<td class="subheader"><?php echo t('Type')?> <span class="required">*</span></td>
	<td class="subheader"><?php echo t('Searchable?')?> <span class="required">*</span></td>
</tr>	
<tr>
	<td style="width: 33%"><input type="text" name="akHandle" style="width: 100%" value="<?php echo $_POST['akHandle']?>" /></td>
	<td style="width: 33%"><select name="akType" style="width: 100%" onchange="if (this.value == 'SELECT') { document.getElementById('akValues').disabled = false; document.getElementById('reqValues').style.display='inline'; } else {  document.getElementById('reqValues').style.display='none'; document.getElementById('akValues').disabled = true; }">
		<option value="TEXT"<?php  if ($_POST['akType'] == 'TEXT') { ?> selected<?php  } ?>><?php echo t('Text Box')?></option>
		<option value="BOOLEAN"<?php  if ($_POST['akType'] == 'BOOLEAN') { ?> selected<?php  } ?>><?php echo t('Check Box')?></option>
		<option value="SELECT"<?php  if ($_POST['akType'] == 'SELECT') { ?> selected<?php  } ?>><?php echo t('Select Menu')?></option>
		<option value="SELECT_ADD"<?php  if ($_POST['akType'] == 'SELECT_ADD') { ?> selected<?php  } ?>><?php echo t('Select Menu + Add Option')?></option>
		<option value="DATE"<?php  if ($_POST['akType'] == 'DATE') { ?> selected <?php  } ?>><?php echo t('Date')?></option>
		<option value="IMAGE_FILE"<?php  if ($_POST['akType'] == 'IMAGE_FILE') { ?> selected <?php  } ?>><?php echo t('Image/File')?></option>
	</select></td>
	<td style="width: 33%"><input type="checkbox" name="akSearchable" style="vertical-align: middle" <?php  if ($_POST['akSearchable']) { ?> checked <?php  } ?> /> <?php echo t('Yes, include this field in the search index.')?></td>
</tr>
<tr>
	<td class="subheader" colspan="3"><?php echo t('Name')?> <span class="required">*</span></td>
</tr>
<tr>
	<td colspan="3"><input type="text" name="akName" style="width: 100%" value="<?php echo $_POST['akName']?>" /></td>
</tr>
<tr>
	<td class="subheader" colspan="3"><?php echo t('Values')?> <span class="required" id="reqValues" <?php  if ($_POST['akType'] != 'SELECT') { ?> style="display: none"<?php  } ?>>*</span></td>
</tr>
<tr>
	<td colspan="3">
    	<textarea id="akValues" name="akValues" style="width: 100%" <?php  if ($_POST['akType'] != 'SELECT') { ?> disabled="disabled" <?php  } ?>><?php echo $_POST['akValues']?></textarea>
        <br/>(<?php echo t('For select types only - separate menu options with line breaks')?>)
    </td>
</tr>
<tr>
	<td colspan="3" class="header">

	<a href="<?php echo $this->url('/dashboard/pages/types')?>" class="ccm-button-left"><span><?php echo t('Cancel')?></span></a>
	<a href="javascript:void(0)" onclick="$('#ccm-add-attribute').get(0).submit()" class="ccm-button-right"><span><?php echo t('Add')?></span></a>
	
	</td>
</tr>
</table>
</div>

<br>
</form>	
</div>


<?php  } ?>