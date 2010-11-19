<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

$form = Loader::helper('form');
Loader::model('attribute/categories/collection');
$attribs = CollectionAttributeKey::getList();

$pages = array();
if (is_array($_REQUEST['cID'])) {
	foreach($_REQUEST['cID'] as $cID) {
		$c = Page::getByID($cID);
		$cp = new Permissions($c);
		if ($cp->canWrite()) {
			$pages[] = $c;
		}
	}
}

if ($_POST['task'] == 'update_extended_attribute') {
	$cakID = $_REQUEST['cakID'];
	$value = ''; 
	
	$ak = CollectionAttributeKey::get($cakID);
	foreach($pages as $c) {
		$cp = new Permissions($c);
		if ($cp->canWrite()) {
			$ak->saveAttributeForm($c);
			$c->reindex();

		}
	}
	$val = $c->getAttributeValueObject($ak);
	print $val->getValue('display');	
	exit;
} 

if ($_POST['task'] == 'clear_extended_attribute') {

	$cakID = $_REQUEST['cakID'];
	$value = ''; 
	
	$ak = CollectionAttributeKey::get($cakID);
	foreach($pages as $c) {
		$cp = new Permissions($c);
		if ($cp->canWrite()) {
			$c->clearAttribute($ak);
			$c->reindex();
		}
	}
	
	print '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
	exit;
}


function printAttributeRow($ak) {
	global $pages, $form;
	
	$value = '';
	for ($i = 0; $i < count($pages); $i++) {
		$lastValue = $value;
		$c = $pages[$i];
		$vo = $c->getAttributeValueObject($ak);
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
	if ($ak->isAttributeKeyEditable()) { 
	$type = $ak->getAttributeType();
	$hiddenFIDfields='';
	foreach($pages as $c) {
		$hiddenfields.=' '.$form->hidden('cID[]' , $c->getCollectionID()).' ';
	}	
	
	$html = '
	<tr class="ccm-attribute-editable-field">
		<th><a href="javascript:void(0)">' . $ak->getAttributeKeyName() . '</a></th>
		<td width="100%" class="ccm-attribute-editable-field-central"><div class="ccm-attribute-editable-field-text">' . $text . '</div>
		<form method="post" action="' . REL_DIR_FILES_TOOLS_REQUIRED . '/pages/bulk_metadata_update">
			<input type="hidden" name="cakID" value="' . $ak->getAttributeKeyID() . '" />
			'.$hiddenfields.'
			<input type="hidden" name="task" value="update_extended_attribute" />
			<div class="ccm-attribute-editable-field-form ccm-attribute-editable-field-type-' . strtolower($type->getAttributeTypeHandle()) . '">
			' . $ak->render('form', $vo, true) . '
			</div>
		</form>
		</td>
		<td class="ccm-attribute-editable-field-save"><a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/edit_small.png" width="16" height="16" class="ccm-attribute-editable-field-save-button" /></a>
		<a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/close.png" width="16" height="16" class="ccm-attribute-editable-field-clear-button" /></a>
		<img src="' . ASSETS_URL_IMAGES . '/throbber_white_16.gif" width="16" height="16" class="ccm-attribute-editable-field-loading" />
		</td>
	</tr>';
	
	} else {

	$html = '
	<tr>
		<th>' . $ak->getAttributeKeyName() . '</th>
		<td width="100%" colspan="2">' . $text . '</td>
	</tr>';	
	}
	print $html;
}

if (!isset($_REQUEST['reload'])) { ?>
	<div id="ccm-page-properties-wrapper">
<?php  } ?>

<h1><?php echo t('Page Details')?></h1>


<div id="ccm-page-properties">

<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">
<?php 

foreach($attribs as $at) {

	printAttributeRow($at);

}

?>
</table>

<br/>  

</div>

<script type="text/javascript">
$(function() { 
	ccm_activateEditablePropertiesGrid();  
});
</script>

<?php 
if (!isset($_REQUEST['reload'])) { ?>
</div>
<?php  }
