<?php 
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
	
	if ($ak->getAttributeKeyType() == 'DATE') {
		$dt = Loader::helper('form/date_time');
		$value = $dt->translate('fakID_' . $fakID);
	} else if (is_array($_REQUEST['fakID_' . $fakID])) {
		foreach($_REQUEST['fakID_' . $fakID] as $val) {
			$value .= $val  . "\n";
		}
	} else {
		$value = $_REQUEST['fakID_' . $fakID] ;
	}
	foreach($files as $f){
		$fv=$f->getVersionToModify();
		$fv->setAttribute($ak, $value);
	}
	$fv->populateAttributes();
	print $fv->getAttribute($ak, true) ;
	
	exit;
} 

function printCorePropertyRow($title, $field, $value, $formText) {
	global $previewMode, $f, $fp, $files, $form;
	if ($value == '') {
		$text = '<div class="ccm-file-manager-field-none">' . t('None') . '</div>';
	} else { 
		$text = htmlentities( $value, ENT_QUOTES, APP_CHARSET);
	}

	if ($fp->canWrite() && (!$previewMode)) {
	
	$hiddenFIDfields='';
	foreach($files as $f) {
		$hiddenFIDfields.=' '.$form->hidden('fID[]' , $f->getFileID()).' ';
	}
	
	$html = '
	<tr class="ccm-file-manager-editable-field">
		<th><a href="javascript:void(0)">' . $title . '</a></th>
		<td width="100%" class="ccm-file-manager-editable-field-central"><div class="ccm-file-manager-editable-field-text">' . $text . '</div>
		<form method="post" action="' . REL_DIR_FILES_TOOLS_REQUIRED . '/files/bulk_properties">
			<input type="hidden" name="attributeField" value="' . $field . '" /> 
			'.$hiddenFIDfields.'
			<input type="hidden" name="task" value="update_core" />
			<div class="ccm-file-manager-editable-field-form ccm-file-manager-editable-field-type-text">
			' . $formText . '
			</div>
		</form>
		</td>
		<td class="ccm-file-manager-editable-field-save"><a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/edit_small.png" width="16" height="16" class="ccm-file-manager-editable-field-save-button" /></a>
		<img src="' . ASSETS_URL_IMAGES . '/throbber_white_16.gif" width="16" height="16" class="ccm-file-manager-editable-field-loading" />
		</td>
	</tr>';
	
	} else {
		$html = '
		<tr>
			<th>' . $title . '</th>
			<td width="100%" colspan="2">' . $text . '</td>
		</tr>';	
	}
	
	print $html;
}

function printFileAttributeRow($ak, $fv) {
	global $previewMode, $f, $fp, $files, $form, $defaultPropertyVals; 
	//$value = $fv->getAttribute($ak, true);
	$value = $defaultPropertyVals['ak'.$ak->getAttributeKeyID()];
	if ($value == '') {
		$text = '<div class="ccm-file-manager-field-none">' . t('None') . '</div>';
	} else {
		$text = $value;
	}
	if ($ak->isAttributeKeyEditable() && $fp->canWrite() && (!$previewMode)) { 
	
	$hiddenFIDfields='';
	foreach($files as $f) {
		$hiddenFIDfields.=' '.$form->hidden('fID[]' , $f->getFileID()).' ';
	}	
	
	$html = '
	<tr class="ccm-file-manager-editable-field">
		<th><a href="javascript:void(0)">' . $ak->getAttributeKeyName() . '</a></th>
		<td width="100%" class="ccm-file-manager-editable-field-central"><div class="ccm-file-manager-editable-field-text">' . $text . '</div>
		<form method="post" action="' . REL_DIR_FILES_TOOLS_REQUIRED . '/files/bulk_properties">
			<input type="hidden" name="fakID" value="' . $ak->getAttributeKeyID() . '" />
			'.$hiddenFIDfields.'
			<input type="hidden" name="task" value="update_extended_attribute" />
			<div class="ccm-file-manager-editable-field-form ccm-file-manager-editable-field-type-' . strtolower($ak->getAttributeKeyType()) . '">
			' . $ak->outputHTML($fv) . '
			</div>
		</form>
		</td>
		<td class="ccm-file-manager-editable-field-save"><a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/edit_small.png" width="16" height="16" class="ccm-file-manager-editable-field-save-button" /></a>
		<img src="' . ASSETS_URL_IMAGES . '/throbber_white_16.gif" width="16" height="16" class="ccm-file-manager-editable-field-loading" />
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
	<div id="ccm-file-properties-wrapper">
<?php  } ?>

<h1><?php echo t('File Details')?></h1>


<div id="ccm-file-properties">
<h2><?php echo t('Basic Properties')?></h2>
<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">  

<?php 

printCorePropertyRow(t('Title'), 'fvTitle', $defaultPropertyVals['title'], $form->text('fvTitle', $defaultPropertyVals['title']));
printCorePropertyRow(t('Description'), 'fvDescription', $defaultPropertyVals['description'], $form->textarea('fvDescription', $defaultPropertyVals['description']));
printCorePropertyRow(t('Tags'), 'fvTags', $defaultPropertyVals['tags'], $form->textarea('fvTags', $defaultPropertyVals['tags']));

?>

</table>


<?php  

if (count($attribs) > 0) { ?>

<br/>

<h2><?php echo t('Other Properties')?></h2>
<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">
<?php 

foreach($attribs as $at) {

	printFileAttributeRow($at, $fv);

}

?>
</table>
<?php  } ?>

<br/>  

</div>

<script type="text/javascript">
$(function() { 
	ccm_alActiveEditableProperties();  
});
</script>

<?php 
if (!isset($_REQUEST['reload'])) { ?>
</div>
<?php  }
