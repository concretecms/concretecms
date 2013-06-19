<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$tp = new TaskPermission();

Loader::model('attribute/categories/user');
$attribs = UserAttributeKey::getEditableList();
$sk = PermissionKey::getByHandle('access_user_search');
$ek = PermissionKey::getByHandle('edit_user_properties');

$tp = new TaskPermission();
if (!$tp->canEditUserProperties()) { 
	die(t("Access Denied."));
}

$users = array();
if (is_array($_REQUEST['uID'])) {
	foreach($_REQUEST['uID'] as $uID) {
		$ui = UserInfo::getByID($uID);
		$users[] = $ui;
	}
}

foreach($users as $ui) {
	if (!$sk->validate($ui)) { 
		die(t("Access Denied."));
	}
}

if ($_POST['task'] == 'update_extended_attribute') {
	$fakID = $_REQUEST['fakID'];
	$value = ''; 
	
	$ak = UserAttributeKey::get($fakID);
	foreach($users as $ui) {
		if ($ek->validate($ak)) { 
			$ak->saveAttributeForm($ui);
		}
	}
	$val = $ui->getAttributeValueObject($ak);
	print $val->getValue('display');	
	exit;
} 

if ($_POST['task'] == 'clear_extended_attribute') {

	$fakID = $_REQUEST['fakID'];
	$value = ''; 
	
	$ak = UserAttributeKey::get($fakID);
	foreach($users as $ui) {
		if ($ek->validate($ak)) { 
			$ui->clearAttribute($ak);
		}
	}
	print '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
	exit;
}


function printAttributeRow($ak, $ek) {
	global $users, $form;
	
	$value = '';
	for ($i = 0; $i < count($users); $i++) {
		$lastValue = $value;
		$ui = $users[$i];
		$vo = $ui->getAttributeValueObject($ak);
		if (is_object($vo)) {
			$value = $vo->getValue('display');
			if ($i > 0 ) {
				if ($lastValue != $value) {
					$value = '<div class="ccm-attribute-field-none">' . t('Multiple Values') . '</div>';
					break;
				}
			}
		}
	}	
	
	if ($value == '') {
		$text = '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
	} else {
		$text = $value;
	}
	if ($ak->isAttributeKeyEditable() && $ek->validate($ak)) { 
	$type = $ak->getAttributeType();
	$hiddenFIDfields='';
	foreach($users as $ui) {
		$hiddenfields.=' '.$form->hidden('uID[]' , $ui->getUserID()).' ';
	}	
	
	$html = '
	<tr class="ccm-attribute-editable-field">
		<td width="250" style="vertical-align: middle"><strong><a href="javascript:void(0)">' . tc('AttributeKeyName', $ak->getAttributeKeyName()) . '</a></strong></td>
		<td style="vertical-align: middle" class="ccm-attribute-editable-field-central"><div class="ccm-attribute-editable-field-text">' . $text . '</div>
		<form method="post" action="' . REL_DIR_FILES_TOOLS_REQUIRED . '/users/bulk_properties">
			<input type="hidden" name="fakID" value="' . $ak->getAttributeKeyID() . '" />
			'.$hiddenfields.'
			<input type="hidden" name="task" value="update_extended_attribute" />
			<div class="ccm-attribute-editable-field-form ccm-attribute-editable-field-type-' . strtolower($type->getAttributeTypeHandle()) . '">
			' . $ak->render('form', $vo, true) . '
			</div>
		</form>
		</td>
		<td class="ccm-attribute-editable-field-save" width="30"><a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/edit_small.png" width="16" height="16" class="ccm-attribute-editable-field-save-button" /></a>
		<a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/close.png" width="16" height="16" class="ccm-attribute-editable-field-clear-button" /></a>
		<img src="' . ASSETS_URL_IMAGES . '/throbber_white_16.gif" width="16" height="16" class="ccm-attribute-editable-field-loading" />
		</td>
	</tr>';
	
	} else {

	$html = '
	<tr>
		<td width="250"><strong>' . tc('AttributeKeyName', $ak->getAttributeKeyName()) . '</strong></td>
		<td style="vertical-align: middle" class="ccm-attribute-editable-field-central" colspan="2">' . $text . '</td>
	</tr>';	
	}
	print $html;
}

if (!isset($_REQUEST['reload'])) { ?>
	<div id="ccm-user-properties-wrapper">
<? } ?>

<div id="ccm-user-properties" class="ccm-ui">

<table border="0" cellspacing="0" cellpadding="0" width="100%" class="table table-striped">
<thead>
<tr>
	<th colspan="3"><?=t('User Attributes')?></th>
</tr>
</thead>
<tbody>
<?

foreach($attribs as $at) {

	printAttributeRow($at, $ek);

}

?>
</tbody>
</table>

<br/>  

</div>

<script type="text/javascript">
$(function() { 
	ccm_activateEditablePropertiesGrid();  
});
</script>

<?
if (!isset($_REQUEST['reload'])) { ?>
</div>
<? }
