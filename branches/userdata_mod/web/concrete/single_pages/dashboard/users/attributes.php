<?
if (!ENABLE_DEFINABLE_USER_ATTRIBUTES) {
	$this->controller->redirect('/dashboard');
}

$section = 'user_attributes';
Loader::model('user_attributes');
if ($_REQUEST['task'] == 'edit') {
	$ak = UserAttributeKey::get($_REQUEST['ukID']);
	if (is_object($ak)) { 		
		if ($_POST['update']) {
		
			$ukType = $_POST['ukType'];
			$ukName = $_POST['ukName'];
			$ukHandle = $_POST['ukHandle'];
			$ukHidden = $_POST['ukHidden'];
			$ukValues = $_POST['ukValues'];
			$ukRequired = $_POST['ukRequired'];
			$ukPrivate = $_POST['ukPrivate'];
			$ukDisplayedOnRegister = $_POST['ukDisplayedOnRegister'];
			
		} else {
			
			$ukType = $ak->getKeyType();
			$ukName = $ak->getKeyName();
			$ukHandle = $ak->getKeyHandle();
			$ukHidden = $ak->isKeyHidden();
			$ukValues = $ak->getKeyValues();
			$ukRequired = $ak->isKeyRequired();
			$ukPrivate = $ak->isKeyPrivate();
			$ukDisplayedOnRegister = $ak->isKeyDisplayedOnRegister();
		
		}
		
		$editMode = true;
	}
}

$txt = Loader::helper('text');

if ($_POST['add'] || $_POST['update']) {
	$ukHandle = $txt->sanitize($_POST['ukHandle']);
	$ukName = $txt->sanitize($_POST['ukName']);
	if ($_POST['ukType'] == 'HTML') {
		$ukValues = $_POST['ukValues'];
	} else {
		$ukValues = $txt->sanitize($_POST['ukValues']);
	}
	$ukType = $txt->sanitize($_POST['ukType']);
	$ukHidden = $_POST['ukHidden'] ? 1 : 0;
	$ukRequired = $_POST['ukRequired'] ? 1 : 0;
	$ukPrivate = $_POST['ukPrivate'] ? 1 : 0;
	$ukDisplayedOnRegister = $_POST['ukDisplayedOnRegister'] ? 1 : 0;
	
	$error = array();
	if (!$ukHandle) {
		$error[] = "Handle required.";
	}
	if (!$ukName) {
		$error[] = "Name required.";
	}
	if (!$ukType) {
		$error[] = "Type required.";
	}
	if (($ukType == 'SELECT' || $ukType == 'RADIO' || $ukType == 'HTML') && !$ukValues) {
		$error[] = "A select attribute must have at least one option.";
	}
	
	if (count($error) == 0) {
		if ($_POST['add']) {
			if ($ukHandle) {
				if (UserAttributeKey::inUse($ukHandle)) {
					$error[] = "A questionnaire item with the handle '" . $ukHandle . "' already exists.";
				}
			}
			if (count($error) == 0) {
				$ck = UserAttributeKey::add($ukHandle, $ukName, $ukRequired, $ukPrivate, $ukDisplayedOnRegister, $ukHidden, $ukValues, $ukType);
				$this->controller->redirect('/dashboard/users?created_attribute=1');
			}
		} else if (is_object($ak)) {
			$ak = $ak->update($ukHandle, $ukName, $ukRequired, $ukPrivate, $ukDisplayedOnRegister, $ukHidden, $ukValues, $ukType);
			$this->controller->redirect('/dashboard/users?updated_attribute=1');
		}		
	}
}

if ($_REQUEST['task'] == 'delete') { 
	$ck = UserAttributeKey::get($_REQUEST['ukID']);
	if (is_object($ck)) {
		$ck->delete();
		$this->controller->redirect('/dashboard/users?attribute_deleted=1');
		exit;
	}
}

if ($_GET['created']) {
	$message = "Attribute Key Created.";
} else if ($_GET['deleted']) { 
	$message = "Attribute Key Deleted.";
} else if ($_GET['updated']) {
	$message = "Attribute Key Updated.";
}

$attribs = UserAttributeKey::getList(true);


if ($editMode) { ?>	
	
<h1><span>Update User Attribute</span></h1>
<div class="ccm-dashboard-inner">

	<form method="post" id="ccm-attribute-update" action="<?=$this->url('/dashboard/users/attributes/')?>">
	<input type="hidden" name="ukID" value="<?=$_REQUEST['ukID']?>" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="update" value="1" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader">Handle <span class="required">*</span></td>
		<td class="subheader">Type <span class="required">*</span></td>
		<td class="subheader">Hidden?</td>
		<td class="subheader">Required?</td>
	</tr>	
	<tr>
		<td><input type="text" name="ukHandle" style="width: 100%" value="<?=$ukHandle?>" /></td>
		<td><select name="ukType" style="width: 100%" onchange="disableValues(this)">
			<option value="TEXT"<? if ($ukType == 'TEXT') { ?> selected<? } ?>>Text Box</option>
			<option value="TEXTAREA"<? if ($ukType == 'TEXTAREA') { ?> selected<? } ?>>Text Field</option>
			<option value="BOOLEAN"<? if ($ukType == 'BOOLEAN') { ?> selected<? } ?>>Check Box</option>
			<option value="SELECT"<? if ($ukType == 'SELECT') { ?> selected<? } ?>>Select Menu</option>
			<option value="RADIO"<? if ($ukType == 'RADIO') { ?> selected<? } ?>>Radio Buttons Menu</option>
			<option value="HTML"<? if ($ukType == 'HTML') { ?> selected<? } ?>>HTML Text (Not Interactive)</option>
		</select></td>
		<td><input type="checkbox" value="1" name="ukHidden" style="vertical-align: middle" <? if ($ukHidden) { ?> checked <? } ?> /> Yes</td>
		<td><input type="checkbox" value="1" name="ukRequired" style="vertical-align: middle" <? if ($ukType == 'HTML') { ?> disabled <? } ?> <? if ($ukRequired) { ?> checked <? } ?> /> Yes</td>
	</tr>
	<tr>	
		<td class="subheader" colspan="2">Name <span class="required">*</span></td>
		<td class="subheader">Private?</td>
		<td class="subheader">Registration Q?</td>
	</tr>
	<tr>
		<td colspan="2"><input type="text" name="ukName" style="width: 100%" value="<?=$ukName?>" /></td>
		<td><input type="checkbox" name="ukPrivate" value="1" style="vertical-align: middle" <? if ($ukType == 'HTML') { ?> disabled <? } ?>  <? if ($ukPrivate) { ?> checked <? } ?> /> Yes</td>
		<td><input type="checkbox" value="1" name="ukDisplayedOnRegister" style="vertical-align: middle" <? if ($ukDisplayedOnRegister) { ?> checked <? } ?> /> Yes</td>
	</tr>
	<tr>
		<td class="subheader" colspan="4">Values <span class="required" id="reqValues" <? if ($ukType != 'SELECT' && $ukType != 'RADIO' && $ukType != "HTML") { ?> style="display: none"<? } ?>>*</span></td>
	</tr>
	<tr>
		<td colspan="4"><textarea id="ukValues" <? if ($ukType != 'SELECT' && $ukType != 'RADIO' && $ukType != "HTML") { ?> disabled <? } ?> name="ukValues" style="width: 100%; height: 120px"><?=$ukValues?></textarea><br/>
		(For select and radio types only - separate menu options with a line break.)<br/>
		HTML Text is used in displaying the questionnaire - it is <b>not</b> an interactive question type.</td>
	</tr>
	<tr>
		<td colspan="4" class="header">
		<a href="<?=$this->url('/dashboard/users')?>" class="ccm-button-left"><span>Cancel</span></a>
		<a href="javascript:void(0)" onclick="$('#ccm-attribute-update').get(0).submit()" class="ccm-button-right"><span>Update</span></a>
		</td>		
	</tr>
	</table>
	</div>
	
	<br>
	
	<? if ($ak->getNumEntries() == 0) { ?>
	
	<div style="float: left; width: 200px; padding-top: 7px"><strong>This field has not been used yet. </strong></div>
	<a href="<?=$this->url('/dashboard/users/attributes?ukID=' . $_REQUEST['ukID'] . '&task=delete')?>" class="ccm-button-left"><span>Delete</span></a>
	
	<div class="ccm-spacer">&nbsp;</div>
	
	<? } ?>
	</form>	

</div>

<? 

} else { ?>

<h1><span>Add User Attribute</span></h1>
<div class="ccm-dashboard-inner">

<form method="post" id="ccm-user-add-attribute" action="<?=$this->url('/dashboard/users/attributes/')?>"><input type="hidden" name="add" value="1" />

<div style="margin:0px; padding:0px; width:100%; height:auto" >	
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader">Handle <span class="required">*</span></td>
	<td class="subheader">Type <span class="required">*</span></td>
	<td class="subheader">Hidden?</td>
	<td class="subheader">Required?</td>
</tr>	
<tr>
	<td><input type="text" name="ukHandle" style="width: 100%" value="<?=$_POST['ukHandle']?>" /></td>
	<td><select name="ukType" style="width: 100%"  onchange="disableValues(this)">
		<option value="TEXT"<? if ($_POST['ukType'] == 'TEXT') { ?> selected<? } ?>>Text Box</option>
		<option value="TEXTAREA"<? if ($_POST['ukType'] == 'TEXTAREA') { ?> selected<? } ?>>Text Area</option>
		<option value="BOOLEAN"<? if ($_POST['ukType'] == 'BOOLEAN') { ?> selected<? } ?>>Check Box</option>
		<option value="SELECT"<? if ($_POST['ukType'] == 'SELECT') { ?> selected<? } ?>>Select Menu</option>
		<option value="RADIO"<? if ($_POST['ukType'] == 'RADIO') { ?> selected<? } ?>>Radio Button Menu</option>
		<option value="HTML"<? if ($_POST['ukType'] == 'HTML') { ?> selected<? } ?>>HTML Text (Not Interactive)</option>
	</select></td>
	<td><input type="checkbox" name="ukHidden" value="1" style="vertical-align: middle" <? if ($_POST['ukHidden']) { ?> checked <? } ?> /> Yes</td>
	<td><input type="checkbox" value="1" name="ukRequired" style="vertical-align: middle" <? if ($_POST['ukRequired']) { ?> checked <? } ?> /> Yes</td>

</tr>
<tr>
	<td class="subheader" colspan="2">Name <span class="required">*</span></td>
	<td class="subheader">Private?</td>
	<td class="subheader">Registration Q?</td>
</tr>
<tr>
	<td colspan="2"><input type="text" name="ukName" style="width: 100%" value="<?=$_POST['ukName']?>" /></td>
	<td><input type="checkbox" name="ukPrivate" value="1" style="vertical-align: middle" <? if ($_POST['ukPrivate']) { ?> checked <? } ?> /> Yes</td>
	<td><input type="checkbox" value="1" name="ukDisplayedOnRegister" style="vertical-align: middle" <? if ($_POST['ukDisplayedOnRegister'] || (!$_POST)) { ?> checked <? } ?> /> Yes</td>

</tr>
<tr>
	<td class="subheader" colspan="4">Values <span class="required" id="reqValues" <? if ($_POST['ukType'] != 'SELECT' && $_POST['ukType'] != 'RADIO') { ?> style="display: none"<? } ?>>*</span></td>
</tr>
<tr>
	<td colspan="4"><textarea id="ukValues" <? if ($_POST['ukType'] != 'SELECT' && $_POST['ukType'] != 'RADIO') { ?> disabled <? } ?> name="ukValues" style="width: 100%; height: 120px"><?=$_POST['ukValues']?></textarea>
	<br/>(For select and radio types only - separate menu options with a line break.)<br/>
	HTML Text is used in displaying the questionnaire - it is <b>not</b> an interactive question type.</td>
</tr>
<tr>
	<td colspan="4" class="header">
	<a href="<?=$this->url('/dashboard/users')?>" class="ccm-button-left"><span>Cancel</span></a>
	<a href="javascript:void(0)" onclick="$('#ccm-user-add-attribute').get(0).submit()" class="ccm-button-right"><span>Add User Attribute</span></a>
</tr>
</table>
</div>

<br>
</form>	
</div>

<? } ?>

<br/>

<script type="text/javascript">
	disableValues = function(obj) {
		if (obj.value == 'HTML') { 
			document.forms[0].ukPrivate.checked=0;
			document.forms[0].ukPrivate.disabled=true;
			document.forms[0].ukRequired.checked=0;
			document.forms[0].ukRequired.disabled=true;
			document.getElementById("ukValues").disabled = false;
			document.getElementById('reqValues').style.display='inline';
			
		} else if (obj.value == 'SELECT' || obj.value == 'RADIO') { 
			document.getElementById("ukValues").disabled = false;
			document.getElementById('reqValues').style.display='inline';
			document.forms[0].ukPrivate.disabled = false;
			document.forms[0].ukRequired.disabled = false;
		} else {
			document.getElementById("ukValues").disabled = true;
			document.getElementById('reqValues').style.display='none';
			
			document.forms[0].ukPrivate.disabled=false;
			document.forms[0].ukRequired.disabled=false;
		}
	}
	</script>