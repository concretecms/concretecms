<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');
Loader::model("file_attributes");
$previewMode = false;

$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(_("Access Denied."));
}  

$attribs = FileAttributeKey::getUserAddedList();

$files = array();
$extensions = array();
$file_versions = array();
$searchInstance = $_REQUEST['searchInstance'];


//load all the requested files
if (is_array($_REQUEST['fID'])) {
	foreach($_REQUEST['fID'] as $fID) {
		$f = File::getByID($fID);
		$fp = new Permissions($f);
		if ($fp->canRead()) {
			$files[] = $f;
			$extensions[] = strtolower($f->getExtension()); 
		}
	}
} else {
	$f = File::getByID($_REQUEST['fID']);
	$fp = new Permissions($f);
	if ($fp->canRead()) {
		$files[] = $f;
		$extensions[] = strtolower($f->getExtension()); 
	}
} 


/*

//the attributes interface needs a file version
$fv = $f->getVersionToModify();

//Default Values - if all the selected files share the same property, then display, otherwise leave it blank
$defaultPropertyVals=array();
foreach($files as $f){
	$fv = $f->getVersionToModify();
	$title=$fv->getTitle();
	if(!strlen($defaultPropertyVals['title']) || $defaultPropertyVals['title']==$title) 
		 $defaultPropertyVals['title']=$title;
	else $defaultPropertyVals['title']='MIXED VALUES'; 
	
	$description=$fv->getDescription(); 
	if(!strlen($defaultPropertyVals['description']) || $defaultPropertyVals['description']==$description) 
		 $defaultPropertyVals['description']=$description;
	else $defaultPropertyVals['description']='MIXED VALUES'; 
	
	$tags=$fv->getTags(); 
	if(!strlen($defaultPropertyVals['tags']) || $defaultPropertyVals['tags']==$tags) 
		 $defaultPropertyVals['tags']=$tags;
	else $defaultPropertyVals['tags']='MIXED VALUES';	 
	
	foreach($attribs as $ak){
		$akID=$ak->getAttributeKeyID();
		$attrVal = $fv->getAttribute($ak, true); 
		if(!strlen($defaultPropertyVals['ak'.$akID]) || $defaultPropertyVals['ak'.$akID]==$attrVal) 
			 $defaultPropertyVals['ak'.$akID]=$attrVal;
		else $defaultPropertyVals['ak'.$akID]='MIXED VALUES';		
	}
}
foreach($defaultPropertyVals as $key=>$val)
	if($val=='MIXED VALUES')  $defaultPropertyVals[$key]='';



 
if ($_POST['task'] == 'update_core' && $fp->canWrite() && (!$previewMode)) { 
 
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

if ($_POST['task'] == 'update_extended_attribute' && $fp->canWrite() && (!$previewMode)) {
	$fv = $f->getVersionToModify();
	$fakID = $_REQUEST['fakID'];
	$value = ''; 
	
	$ak = FileAttributeKey::get($fakID);
	foreach($files as $f){
		$fv=$f->getVersionToModify();
		$ak->saveAttributeForm($fv);
	}
	$fv->populateAttributes();
	$val = $fv->getAttributeValueObject($ak);
	print $val->getValue('display');
	
	exit;
} 

if ($_POST['task'] == 'clear_extended_attribute' && $fp->canWrite() && (!$previewMode)) {

	$fv = $f->getVersionToModify();
	$fakID = $_REQUEST['fakID'];
	$value = ''; 
	
	$ak = FileAttributeKey::get($fakID);
	foreach($files as $f){
		$fv=$f->getVersionToModify();
		$fv->clearAttribute($ak);
	}
	$fv->populateAttributes();
	$val = $fv->getAttributeValueObject($ak);

	print '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
	exit;
}


function printFileAttributeRow($ak, $fv) {
	global $previewMode, $f, $fp, $files, $form, $defaultPropertyVals; 
	$vo = $fv->getAttributeValueObject($ak);
	$value = '';
	if (is_object($vo)) {
		$value = $vo->getValue('display');
	}
	
	if ($value == '') {
		$text = '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
	} else {
		$text = $value;
	}
	if ($ak->isAttributeKeyEditable() && $fp->canWrite() && (!$previewMode)) { 
	$type = $ak->getAttributeType();
	$hiddenFIDfields='';
	foreach($files as $f) {
		$hiddenFIDfields.=' '.$form->hidden('fID[]' , $f->getFileID()).' ';
	}	
	
	$html = '
	<tr class="ccm-attribute-editable-field">
		<th><a href="javascript:void(0)">' . $ak->getAttributeKeyName() . '</a></th>
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

*/


if (!isset($_REQUEST['reload'])) { ?>
	<div id="ccm-file-properties-wrapper">
<? } ?>

<script type="text/javascript">
$(function() {
	$("div.message").show('highlight');
});
</script>

<style type="text/css">
div.ccm-add-files-complete div.message {margin-bottom: 0px}
table.ccm-grid input.ccm-input-text, table.ccm-grid textarea {width: 100%}
table.ccm-grid td {padding-right: 20px; width: 230px}
table.ccm-grid th {width: 70px}
</style>

<form method="post" id="ccm-<?=$searchInstance?>-update-uploaded-details-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/add_to_complete/">

<div class="ccm-add-files-complete">

<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="100%">
	<? if (count($_REQUEST['fID']) == 1) { ?>
		<div class="message"><strong><?=t('1 file uploaded successfully.')?></strong></div>
	<? } else { ?>
		<div class="message"><strong><?=t('%s files uploaded successfully.', count($_REQUEST['fID']))?></strong></div>
	<? } ?>
	</td>
	<td><div style="width: 20px">&nbsp;</div></td>
	<td>
	<?
	$ci = Loader::helper('concrete/interface');
	print $ci->button_js(t('Save Details and Sets'), 'ccm_alSubmitUploadDetailsForm(\'' . $searchInstance . '\')');
	?>
	</td>
</tr>
</table>

</div>

<div class="ccm-spacer">&nbsp;</div>

<?=$form->hidden('task', 'update_uploaded_details')?>
<? foreach($files as $f) { ?>
	<input type="hidden" name="fID[]" value="<?=$f->getFileID();?>" />
<? } ?>
<input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />

<hr/>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td width="50%" valign="top">
		<h1 style="margin-top: 12px"><?=t('File Details')?></h1>
		
		<div id="ccm-file-properties">
		<h2><?=t('Basic Properties')?></h2>
		<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">  
		<tr>
			<th><?=t('Title')?></th>
			<td><?=$form->text('fvTitle')?></td>
		</tr>
		<tr>
			<th><?=t('Description')?></th>
			<td><?=$form->textarea('fvDescription')?></td>
		</tr>
		<tr>
			<th><?=t('Tags')?></th>
			<td><?=$form->textarea('fvTags')?></td>
		</tr>
		</table>
		
		<? 
		
		if (count($attribs) > 0) { ?>
		
		<br/>
		
		<h2><?=t('Other Properties')?></h2>
		<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">
		<?
		foreach($attribs as $at) { ?>
		
		<tr>
			<th><?=$at->getAttributeKeyName()?></th>
			<td><?=$at->render('form', false, true)?>
		</tr>
		
		<? } ?>
		</table>
		<? } ?>
		
		</div>

	</td>
	<td><div style="width: 20px">&nbsp;</div></td>
	<td valign="top">
	
	<? Loader::element('files/add_to_sets', array('disableForm' => true)) ?>

	</td>
</tr>
</table>

</div>
</form>

<script type="text/javascript">
$(function() {
	ccm_alSetupUploadDetailsForm('<?=$searchInstance?>');
});
</script>

<?
if (!isset($_REQUEST['reload'])) { ?>
</div>
<? }
