<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
Loader::model("file_attributes");
$previewMode = false;

$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(t("Unable to access the file manager."));
}  

$attribs = FileAttributeKey::getUserAddedList();

$files = array();
$extensions = array();
$file_versions = array();

//load all the requested files
if (is_array($_REQUEST['fID'])) {
	foreach($_REQUEST['fID'] as $fID) {
		$f = File::getByID($fID);
		$fp = new Permissions($f);
		if ($fp->canViewFile()) {
			$files[] = $f;
			$extensions[] = strtolower($f->getExtension()); 
		}
	}
} else {
	$f = File::getByID($_REQUEST['fID']);
	$fp = new Permissions($f);
	if ($fp->canViewFile()) {
		$files[] = $f;
		$extensions[] = strtolower($f->getExtension()); 
	}
} 

//the attributes interface needs a file version
$fv = $f->getVersionToModify();

//Default Values - if all the selected files share the same property, then display, otherwise leave it blank
$defaultPropertyVals=array();
foreach($files as $f){
	$fv = $f->getVersionToModify();
	$title=$fv->getTitle();
	if(!strlen($defaultPropertyVals['title']) || $defaultPropertyVals['title']==$title) {
		 $defaultPropertyVals['title']=$title;  $defaultPropertyVals['titleValue']=$title;
		} else {
		$defaultPropertyVals['title']='{CCM:MULTIPLE:VALUES}';  $defaultPropertyVals['titleValue']='';
		}
		
	$description=$fv->getDescription(); 
	if(!strlen($defaultPropertyVals['description']) || $defaultPropertyVals['description']==$description) {
		 $defaultPropertyVals['description']=$description; $defaultPropertyVals['descriptionValue']=$description;
	} else {
		 $defaultPropertyVals['description']='{CCM:MULTIPLE:VALUES}';  $defaultPropertyVals['descriptionValue']='';
	}
	
	$tags=$fv->getTags(); 
	if(!strlen($defaultPropertyVals['tags']) || $defaultPropertyVals['tags']==$tags) {
		 $defaultPropertyVals['tags']=$tags;  $defaultPropertyVals['tagsValue']=$tags;
	} else {
		$defaultPropertyVals['tags']='{CCM:MULTIPLE:VALUES}';  $defaultPropertyVals['tagsValue']='';
	}
	
	foreach($attribs as $ak){
		$akID=$ak->getAttributeKeyID();
		$vo = $fv->getAttributeValueObject($ak);
		$attrVal = '';
		if (is_object($vo)) {
			$attrVal = $vo->getValue('display');
		}
		if(!isset($defaultPropertyVals['ak'.$akID]) || $defaultPropertyVals['ak'.$akID]==$attrVal) {
			 $defaultPropertyVals['ak'.$akID]=$attrVal;
		} else {
			$defaultPropertyVals['ak' . $akID]='{CCM:MULTIPLE:VALUES}';  $defaultPropertyVals['ak' . $akID . 'Value']='';
		}
	}
}

if ($_POST['task'] == 'update_core' && $fp->canEditFileProperties() && (!$previewMode)) { 
 
	switch($_POST['attributeField']) {
		case 'fvTitle':
			$text = $_POST['fvTitle'];
			foreach($files as $f){ 
				$fv=$f->getVersionToModify();
				$fv->updateTitle($text); 
			}
			print $text;
			break;
		case 'fvDescription':
			$text = $_POST['fvDescription'];
			foreach($files as $f){
				$fv=$f->getVersionToModify();
				$fv->updateDescription($text);
			}
			print $text;
			break;
		case 'fvTags':
			$text = $_POST['fvTags'];
			foreach($files as $f){
				$fv=$f->getVersionToModify();
				$fv->updateTags($text);
			}
			print $text;
			break;
	} 
	
	exit;
}

if ($_POST['task'] == 'update_extended_attribute' && $fp->canEditFileProperties() && (!$previewMode)) {
	$fv = $f->getVersionToModify();
	$fakID = $_REQUEST['fakID'];
	$value = ''; 
	
	$ak = FileAttributeKey::get($fakID);
	foreach($files as $f){
		$fv=$f->getVersionToModify();
		$ak->saveAttributeForm($fv);
	}
	$val = $fv->getAttributeValueObject($ak);
	print $val->getValue('display');
	
	exit;
} 

if ($_POST['task'] == 'clear_extended_attribute' && $fp->canEditFileProperties() && (!$previewMode)) {

	$fv = $f->getVersionToModify();
	$fakID = $_REQUEST['fakID'];
	$value = ''; 
	
	$ak = FileAttributeKey::get($fakID);
	foreach($files as $f){
		$fv=$f->getVersionToModify();
		$fv->clearAttribute($ak);
	}
	$val = $fv->getAttributeValueObject($ak);

	print '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
	exit;
}


function printCorePropertyRow($title, $field, $value, $formText) {
	global $previewMode, $f, $fp, $files, $form;
	if ($value == '') {
		$text = '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
	} else if ($value == '{CCM:MULTIPLE:VALUES}') { 
		$text = '<div class="ccm-attribute-field-none">' . t('Multiple Values') . '</div>';
	} else { 
		$text = htmlentities( $value, ENT_QUOTES, APP_CHARSET);
	}

	if ($fp->canEditFileProperties() && (!$previewMode)) {
	
	$hiddenFIDfields='';
	foreach($files as $f) {
		$hiddenFIDfields.=' '.$form->hidden('fID[]' , $f->getFileID()).' ';
	}
	
	$html = '
	<tr class="ccm-attribute-editable-field">
		<td><strong><a href="javascript:void(0)">' . $title . '</a></strong></td>
		<td width="100%" class="ccm-attribute-editable-field-central"><div class="ccm-attribute-editable-field-text">' . $text . '</div>
		<form method="post" action="' . REL_DIR_FILES_TOOLS_REQUIRED . '/files/bulk_properties">
			<input type="hidden" name="attributeField" value="' . $field . '" /> 
			'.$hiddenFIDfields.'
			<input type="hidden" name="task" value="update_core" />
			<div class="ccm-attribute-editable-field-form ccm-attribute-editable-field-type-text">
			' . $formText . '
			</div>
		</form>
		</td>
		<td class="ccm-attribute-editable-field-save"><a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/edit_small.png" width="16" height="16" class="ccm-attribute-editable-field-save-button" /></a>
		<img src="' . ASSETS_URL_IMAGES . '/throbber_white_16.gif" width="16" height="16" class="ccm-attribute-editable-field-loading" />
		</td>
	</tr>';
	
	} else {
		$html = '
		<tr>
			<td><strong>' . $title . '</strong></td>
			<td width="100%" colspan="2">' . $text . '</td>
		</tr>';	
	}
	
	print $html;
}

function printFileAttributeRow($ak, $fv, $value) {
	global $previewMode, $f, $fp, $files, $form; 
	$vo = $fv->getAttributeValueObject($ak);
	
	if ($value == '') {
		$text = '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
	} else if ($value == '{CCM:MULTIPLE:VALUES}') { 
		$text = '<div class="ccm-attribute-field-none">' . t('Multiple Values') . '</div>';
		$vo = '';
	} else { 
		$text = $value;
	}

	if ($ak->isAttributeKeyEditable() && $fp->canEditFileProperties() && (!$previewMode)) { 
	$type = $ak->getAttributeType();
	$hiddenFIDfields='';
	foreach($files as $f) {
		$hiddenFIDfields.=' '.$form->hidden('fID[]' , $f->getFileID()).' ';
	}	
	
	$html = '
	<tr class="ccm-attribute-editable-field">
		<td><strong><a href="javascript:void(0)">' . tc('AttributeKeyName', $ak->getAttributeKeyName()) . '</a></strong></td>
		<td width="100%" class="ccm-attribute-editable-field-central"><div class="ccm-attribute-editable-field-text">' . $text . '</div>
		<form method="post" action="' . REL_DIR_FILES_TOOLS_REQUIRED . '/files/bulk_properties">
			<input type="hidden" name="fakID" value="' . $ak->getAttributeKeyID() . '" />
			'.$hiddenFIDfields.'
			<input type="hidden" name="task" value="update_extended_attribute" />
			<div class="ccm-attribute-editable-field-form ccm-attribute-editable-field-type-' . strtolower($type->getAttributeTypeHandle()) . '">
			' . $ak->render('form', $vo, true) . '
			</div>
		</form>
		</td>
		<td class="ccm-attribute-editable-field-save"><a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/edit_small.png" width="16" height="16" class="ccm-attribute-editable-field-save-button" /></a>
		<a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/remove.png" width="16" height="16" class="ccm-attribute-editable-field-clear-button" /></a>
		<img src="' . ASSETS_URL_IMAGES . '/throbber_white_16.gif" width="16" height="16" class="ccm-attribute-editable-field-loading" />
		</td>
	</tr>';
	
	} else {

	$html = '
	<tr>
		<td><strong>' . tc('AttributeKeyName', $ak->getAttributeKeyName()) . '</strong></td>
		<td width="100%" colspan="2">' . $text . '</td>
	</tr>';	
	}
	print $html;
}

if (!isset($_REQUEST['reload'])) { ?>
	<div id="ccm-file-properties-wrapper">
<? } ?>

<script type="text/javascript">
var ccm_activeFileManagerAddCompleteTab = "ccm-file-manager-add-complete-basic";

$(function() {
	$("#ccm-file-manager-add-complete-tabs a").click(function() {
		$("li.active").removeClass('active');
		$("#" + ccm_activeFileManagerAddCompleteTab + "-tab").hide();
		ccm_activeFileManagerAddCompleteTab = $(this).attr('id');
		$(this).parent().addClass("active");
		$("#" + ccm_activeFileManagerAddCompleteTab + "-tab").show();
	});

	$("div.ccm-message").show('highlight');
});
</script>

<style type="text/css">
div.ccm-add-files-complete div.ccm-message {margin-bottom: 0px}
table.ccm-grid input.ccm-input-text, table.ccm-grid textarea {width: 100%}
table.ccm-grid th {width: 70px}

</style>
<div class="ccm-ui">
<? if ($_REQUEST['uploaded']) { ?>
	<div class="block-message alert-message success" style="padding-right: 14px !important"><a class="btn success btn-mini" style="float: right;" onclick="jQuery.fn.dialog.closeTop()"><?=t('Continue')?></a><?=t2('%d file uploaded successfully.', '%d files uploaded successfully.', count($_REQUEST['fID']), count($_REQUEST['fID']))?></div>
<? } ?>

<ul class="tabs" id="ccm-file-manager-add-complete-tabs">
	<li class="active"><a href="javascript:void(0)" id="ccm-file-manager-add-complete-basic"><?=t('Basic Properties')?></a></li>
	<? if (count($attribs) > 0) { ?>
		<li><a href="javascript:void(0)" id="ccm-file-manager-add-complete-attributes"><?=t('Other Properties')?></a></li>
	<? } ?>
	<? if ($_REQUEST['uploaded']) { ?>
		<li><a href="javascript:void(0)" id="ccm-file-manager-add-complete-sets"><?=t('Sets')?></a></li>
	<? } ?>
</ul>

<div id="ccm-file-properties">
<div id="ccm-file-manager-add-complete-basic-tab">
<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">  
<? if (count($files) == 1) { ?>
<tr>
	<td><strong><?=t('ID')?></strong></td>
	<td width="100%" colspan="2"><?=$fv->getFileID()?> <span style="color: #afafaf">(<?=t('Version')?> <?=$fv->getFileVersionID()?>)</span></td>
</tr>
<tr>
	<td><strong><?=t('Filename')?></strong></td>
	<td width="100%" colspan="2"><?=$fv->getFileName()?></td>
</tr>
<tr>
	<td><strong><?=t('URL to File')?></strong></td>
	<td width="100%" colspan="2"><?=$fv->getRelativePath(true)?></td>
</tr>

<tr>
	<td><strong><?=t('Type')?></strong></td>
	<td colspan="2"><?=$fv->getType()?></td>
</tr>

<tr>
	<td><strong><?=t('Size')?></strong></td>
	<td colspan="2"><?=$fv->getSize()?> (<?=t2(/*i18n: %s is a number */ '%s byte', '%s bytes', $fv->getFullSize(), Loader::helper('number')->format($fv->getFullSize()))?>)</td>
</tr>
<? } ?>

<?
printCorePropertyRow(t('Title'), 'fvTitle', $defaultPropertyVals['title'], $form->text('fvTitle', $defaultPropertyVals['titleValue']));
printCorePropertyRow(t('Description'), 'fvDescription', $defaultPropertyVals['description'], $form->textarea('fvDescription', $defaultPropertyVals['descriptionValue']));
printCorePropertyRow(t('Tags'), 'fvTags', $defaultPropertyVals['tags'], $form->textarea('fvTags', $defaultPropertyVals['tagsValue']));
?>

<? if (count($files) == 1) { ?>
<tr>
	<td><strong><?=t('File Preview')?></strong></td>
	<td colspan="2"><?=$fv->getThumbnail(2)?></td>
</tr>
<? } ?>
</table>

</div>

<div id="ccm-file-manager-add-complete-attributes-tab" style="display: none">

<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid" width="100%">  
<?
foreach($attribs as $at) { 
	printFileAttributeRow($at, $fv, $defaultPropertyVals['ak' . $at->getAttributeKeyID()]);
} ?>
</table>

</div>
</div>

<? if ($_REQUEST['uploaded']) { ?>

	<div id="ccm-file-manager-add-complete-sets-tab" style="display: none">	
		<div class="ccm-files-add-to-sets-wrapper"><? Loader::element('files/add_to_sets', array('disableForm' => FALSE, 'disableTitle' => true)) ?></div>
	</div>

<? } ?>

<script type="text/javascript">
$(function() { 
	ccm_activateEditablePropertiesGrid();  
});
</script>

</div>

<?
if (!isset($_REQUEST['reload'])) { ?>
</div>
<? }
