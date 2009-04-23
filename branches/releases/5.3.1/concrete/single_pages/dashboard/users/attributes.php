<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
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
$valt = Loader::helper('validation/token');

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
		$error[] = t("Handle required.");
	}
	if (!$ukName) {
		$error[] = t("Name required.");
	}
	if (!$ukType) {
		$error[] = t("Type required.");
	}
	if (($ukType == 'SELECT' || $ukType == 'RADIO' || $ukType == 'HTML') && !$ukValues) {
		$error[] = t("A select attribute must have at least one option.");
	}

	if (!$valt->validate('add_or_update_user_attribute')) {
		$error[] = $valt->getErrorMessage();
	}

	if (count($error) == 0) {
		if ($_POST['add']) {
			if ($ukHandle) {
				if (UserAttributeKey::inUse($ukHandle)) {
					$error[] = t("A questionnaire item with the handle '%s' already exists.",$ukHandle);
				}
			}
			if (count($error) == 0) {
				$ck = UserAttributeKey::add($ukHandle, $ukName, $ukRequired, $ukPrivate, $ukDisplayedOnRegister, $ukHidden, $ukValues, $ukType);
				$this->controller->redirect('/dashboard/users/attributes?created=1');
			}
		} else if (is_object($ak)) {
			$ak = $ak->update($ukHandle, $ukName, $ukRequired, $ukPrivate, $ukDisplayedOnRegister, $ukHidden, $ukValues, $ukType);
			$this->controller->redirect('/dashboard/users/attributes?updated=1');
		}		
	}
}

if ($_REQUEST['task'] == 'delete') { 
	$ck = UserAttributeKey::get($_REQUEST['ukID']);
	if (is_object($ck)) {
		$ck->delete();
		$this->controller->redirect('/dashboard/users/attributes?deleted=1');
		exit;
	}
}

if ($_GET['created']) {
	$message = t("Attribute Key Created.");
} else if ($_GET['deleted']) { 
	$message = t("Attribute Key Deleted.");
} else if ($_GET['updated']) {
	$message = t("Attribute Key Updated.");
}

$attribs = UserAttributeKey::getList(true);


if ($editMode) { ?>	
	
<h1><span>Update User Attribute</span></h1>
<div class="ccm-dashboard-inner">

	<form method="post" id="ccm-attribute-update" action="<?php echo $this->url('/dashboard/users/attributes/')?>">
	<?php echo $valt->output('add_or_update_user_attribute')?>
	
	<input type="hidden" name="ukID" value="<?php echo $_REQUEST['ukID']?>" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="update" value="1" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader"><?php echo t('Handle')?> <span class="required">*</span></td>
		<td class="subheader"><?php echo t('Type')?> <span class="required">*</span></td>
		<td class="subheader"><?php echo t('Hidden?')?></td>
		<td class="subheader"><?php echo t('Required?')?></td>
	</tr>	
	<tr>
		<td><input type="text" name="ukHandle" style="width: 100%" value="<?php echo $ukHandle?>" /></td>
		<td><select name="ukType" style="width: 100%" onchange="disableValues(this)">
			<option value="TEXT"<?php  if ($ukType == 'TEXT') { ?> selected<?php  } ?>><?php echo t('Text Box')?></option>
			<option value="TEXTAREA"<?php  if ($ukType == 'TEXTAREA') { ?> selected<?php  } ?>><?php echo t('Text Field')?></option>
			<option value="BOOLEAN"<?php  if ($ukType == 'BOOLEAN') { ?> selected<?php  } ?>><?php echo t('Check Box')?></option>
			<option value="SELECT"<?php  if ($ukType == 'SELECT') { ?> selected<?php  } ?>><?php echo t('Select Menu')?></option>
			<option value="RADIO"<?php  if ($ukType == 'RADIO') { ?> selected<?php  } ?>><?php echo t('Radio Buttons Menu')?></option>
			<option value="HTML"<?php  if ($ukType == 'HTML') { ?> selected<?php  } ?>><?php echo t('HTML Text (Not Interactive)')?></option>
		</select></td>
		<td><input type="checkbox" value="1" name="ukHidden" style="vertical-align: middle" <?php  if ($ukHidden) { ?> checked <?php  } ?> /> <?php echo t('Yes') ?> </td>
		<td><input type="checkbox" value="1" name="ukRequired" style="vertical-align: middle" <?php  if ($ukType == 'HTML') { ?> disabled <?php  } ?> <?php  if ($ukRequired) { ?> checked <?php  } ?> /> <?php echo t('Yes')?> </td>
	</tr>
	<tr>	
		<td class="subheader" colspan="2"><?php echo t('Name')?> <span class="required">*</span></td>
		<td class="subheader"><?php echo t('Private?')?></td>
		<td class="subheader"><?php echo t('Registration Q?')?></td>
	</tr>
	<tr>
		<td colspan="2"><input type="text" name="ukName" style="width: 100%" value="<?php echo $ukName?>" /></td>
		<td><input type="checkbox" name="ukPrivate" value="1" style="vertical-align: middle" <?php  if ($ukType == 'HTML') { ?> disabled <?php  } ?>  <?php  if ($ukPrivate) { ?> checked <?php  } ?> /> <?php echo t('Yes')?></td>
		<td><input type="checkbox" value="1" name="ukDisplayedOnRegister" style="vertical-align: middle" <?php  if ($ukDisplayedOnRegister) { ?> checked <?php  } ?> /> <?php echo t('Yes')?></td>
	</tr>
	<tr>
		<td class="subheader" colspan="4"><?php echo t('Values')?> <span class="required" id="reqValues" <?php  if ($ukType != 'SELECT' && $ukType != 'RADIO' && $ukType != "HTML") { ?> style="display: none"<?php  } ?>>*</span></td>
	</tr>
	<tr>
		<td colspan="4">
		<textarea id="ukValues" <?php  if ($ukType != 'SELECT' && $ukType != 'RADIO' && $ukType != "HTML") { ?> disabled <?php  } ?> name="ukValues" style="width: 100%; height: 120px"><?php echo $ukValues?></textarea><br/>
		<?php echo t('(For select and radio types only - separate menu options with a line break.)')?><br/>		
		<?php echo t('HTML Text is used in displaying the questionnaire - it is <b>not</b> an interactive question type.')?>
		</td>
	</tr>
	<tr>
		<td colspan="4" class="header">
		<a href="<?php echo $this->url('/dashboard/users/attributes')?>" class="ccm-button-left"><span><?php echo t('Cancel')?></span></a>
		<a href="javascript:void(0)" onclick="$('#ccm-attribute-update').get(0).submit()" class="ccm-button-right"><span><?php echo t('Update')?></span></a>
		</td>		
	</tr>
	</table>
	</div>
	
	<br>
	
	<?php  if ($ak->getNumEntries() == 0) { ?>
	
	<div style="float: left; width: 200px; padding-top: 7px"><strong><?php echo t('This field has not been used yet.')?> </strong></div>
	<a href="<?php echo $this->url('/dashboard/users/attributes?ukID=' . $_REQUEST['ukID'] . '&task=delete')?>" class="ccm-button-left"><span><?php echo t('Delete')?></span></a>
	
	<div class="ccm-spacer">&nbsp;</div>
	
	<?php  } ?>
	</form>	

</div>

<?php  

} else if (ENABLE_DEFINABLE_USER_ATTRIBUTES) { ?>
	
	<a name="attributes"></a>

	
	<h1><span><?php echo t('User Attributes')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	
	<?php  if (count($attribs) > 0) { ?>

	<p><?php echo t("To set the order for these items on the registration form, click and drag the graphic next to the attribute's name.")?></p>
	
	<div id="user-attributes-list">
	
	<?php 
	foreach($attribs as $ak) { ?>
	<div class="uat" id="item_<?php echo $ak->getKeyID()?>" style="font-size: 12px">
	<img src="<?php echo ASSETS_URL_IMAGES?>/dashboard/uat-<?php echo $ak->getKeyType()?>.gif" width="21" height="21" class="handle" id="handle<?php echo $ak->getKeyID()?>" /><a href="<?php echo $this->url('/dashboard/users/attributes?ukID=' . $ak->getKeyID() . '&task=edit')?>"><?php echo $ak->getKeyName()?></a> (<?php echo $ak->getNumEntries()?>)
	</div>
	
	<?php  } ?>

	</div>
	
	<?php  } else { ?>
		
	<br/><strong><?php echo t('No user attributes defined.')?></strong><br/><br/>
		
	<?php  } ?>

	<div class="ccm-spacer">&nbsp;</div>
	
	</div>


<h1><span><?php echo t('Add User Attribute')?></span></h1>
<div class="ccm-dashboard-inner">

<form method="post" id="ccm-user-add-attribute" action="<?php echo $this->url('/dashboard/users/attributes/')?>"><input type="hidden" name="add" value="1" />
<?php echo $valt->output('add_or_update_user_attribute')?>
	
<div style="margin:0px; padding:0px; width:100%; height:auto" >	
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?php echo t('Handle')?> <span class="required">*</span></td>
	<td class="subheader"><?php echo t('Type')?> <span class="required">*</span></td>
	<td class="subheader"><?php echo t('Hidden?')?></td>
	<td class="subheader"><?php echo t('Required?')?></td>
</tr>	
<tr>
	<td><input type="text" name="ukHandle" style="width: 100%" value="<?php echo $_POST['ukHandle']?>" /></td>
	<td><select name="ukType" style="width: 100%"  onchange="disableValues(this)">
		<option value="TEXT"<?php  if ($_POST['ukType'] == 'TEXT') { ?> selected<?php  } ?>><?php echo t('Text Box')?></option>
		<option value="TEXTAREA"<?php  if ($_POST['ukType'] == 'TEXTAREA') { ?> selected<?php  } ?>><?php echo t('Text Area')?></option>
		<option value="BOOLEAN"<?php  if ($_POST['ukType'] == 'BOOLEAN') { ?> selected<?php  } ?>><?php echo t('Check Box')?></option>
		<option value="SELECT"<?php  if ($_POST['ukType'] == 'SELECT') { ?> selected<?php  } ?>><?php echo t('Select Menu')?></option>
		<option value="RADIO"<?php  if ($_POST['ukType'] == 'RADIO') { ?> selected<?php  } ?>><?php echo t('Radio Button Menu')?></option>
		<option value="HTML"<?php  if ($_POST['ukType'] == 'HTML') { ?> selected<?php  } ?>><?php echo t('HTML Text (Not Interactive)')?></option>
	</select></td>
	<td><input type="checkbox" name="ukHidden" value="1" style="vertical-align: middle" <?php  if ($_POST['ukHidden']) { ?> checked <?php  } ?> /> <?php echo t('Yes')?></td>
	<td><input type="checkbox" value="1" name="ukRequired" style="vertical-align: middle" <?php  if ($_POST['ukRequired']) { ?> checked <?php  } ?> /> <?php echo t('Yes')?></td>

</tr>
<tr>
	<td class="subheader" colspan="2"><?php echo t('Name')?> <span class="required">*</span></td>
	<td class="subheader"><?php echo t('Private?')?></td>
	<td class="subheader"><?php echo t('Registration Q?')?></td>
</tr>
<tr>
	<td colspan="2"><input type="text" name="ukName" style="width: 100%" value="<?php echo $_POST['ukName']?>" /></td>
	<td><input type="checkbox" name="ukPrivate" value="1" style="vertical-align: middle" <?php  if ($_POST['ukPrivate']) { ?> checked <?php  } ?> /> <?php echo t('Yes')?></td>
	<td><input type="checkbox" value="1" name="ukDisplayedOnRegister" style="vertical-align: middle" <?php  if ($_POST['ukDisplayedOnRegister'] || (!$_POST)) { ?> checked <?php  } ?> /> <?php echo t('Yes')?></td>

</tr>
<tr>
	<td class="subheader" colspan="4"><?php echo t('Values')?> <span class="required" id="reqValues" <?php  if ($_POST['ukType'] != 'SELECT' && $_POST['ukType'] != 'RADIO') { ?> style="display: none"<?php  } ?>>*</span></td>
</tr>
<tr>
	<td colspan="4"><textarea id="ukValues" <?php  if ($_POST['ukType'] != 'SELECT' && $_POST['ukType'] != 'RADIO') { ?> disabled <?php  } ?> name="ukValues" style="width: 100%; height: 120px"><?php echo $_POST['ukValues']?></textarea>
	<br /><?php echo t('(For select and radio types only - separate menu options with a line break.)')?><br />	
	<?php echo t('HTML Text is used in displaying the questionnaire - it is <b>not</b> an interactive question type.')?>
	</td>
</tr>
<tr>
	<td colspan="4" class="header">
	<a href="<?php echo $this->url('/dashboard/users/attributes')?>" class="ccm-button-left"><span><?php echo t('Cancel')?></span></a>
	<a href="javascript:void(0)" onclick="$('#ccm-user-add-attribute').get(0).submit()" class="ccm-button-right"><span><?php echo t('Add User Attribute')?></span></a>
</tr>
</table>
</div>

<br>
</form>	
</div>

<?php  } ?>

<br/>

<script type="text/javascript">
$(function() {
	$("div#user-attributes-list").sortable({
		handle: 'img.handle',
		cursor: 'move',
		opacity: 0.5,
		stop: function() {
			var ualist = $(this).sortable('serialize');
			$.post('<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/user_attributes_update.php', ualist, function(r) {

			});
		}
	});
});


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