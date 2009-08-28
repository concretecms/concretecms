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
			$akAllowOtherValues = $_POST['akAllowOtherValues'];
			$akValues = $_POST['akValues'];			
		} else {			
			$akType = $ak->getCollectionAttributeKeyType();
			$akName = $ak->getCollectionAttributeKeyName();
			$akHandle = $ak->getCollectionAttributeKeyHandle();
			$akSearchable = $ak->isCollectionAttributeKeySearchable();
			$akValues = $ak->getCollectionAttributeKeyValues();
			$akAllowOtherValues = $ak->getAllowOtherValues();		
		}		
		$editMode = true;
	}
}

$txt = Loader::helper('text');

if ($_POST['add'] || $_POST['update']) {
	$akHandle = $txt->sanitize($_POST['akHandle']);
	$akName = $txt->sanitize($_POST['akName']);
	//$akValues = $txt->sanitize($_POST['akValues']);
	//$akValues = preg_replace('/\r\n|\r/', "\n", $akValues); // make linebreaks consistant
	$akType = $txt->sanitize($_POST['akType']);
	$akSearchable = $_POST['akSearchable'] ? 1 : 0;
	$akAllowOtherValues = $_POST['akAllowOtherValues'] ? 1 : 0;
	
	//grab the attribute key possible values
	$akValuesArray=array(); 
	foreach($_POST as $key=>$newVal){ 
		if( !strstr($key,'akValue_') || $newVal=='TEMPLATE' ) continue; 
		$originalVal=$_REQUEST['akValueOriginal_'.str_replace('akValue_','',$key)];		
		$akValuesArray[]=$newVal; 
		//change all previous answers
		if($ak) $ak->renameValue($originalVal,$newVal);
	} 
	$akValuesArray=array_unique($akValuesArray);
	$akValues=join("\n",$akValuesArray); 
	
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
	
	if (CollectionAttributeKey::inUse($akHandle)) {
		if ((!is_object($ak)) || ($ak->getCollectionAttributeKeyHandle() != $akHandle)) {
			$error[] = t("An attribute with the handle %s already exists.", $akHandle);
		}
	}

	if (count($error) == 0) {
		if ($_POST['add']) {
			if (count($error) == 0) {
				$ck = CollectionAttributeKey::add($akHandle, $akName, $akSearchable, $akValues, $akType, $akAllowOtherValues);
				$this->controller->redirect('/dashboard/pages/types/?attribute_created=1');
			}
		} else if (is_object($ak)) {
			$ak = $ak->update($akHandle, $akName, $akSearchable, $akValues, $akType, $akAllowOtherValues);
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

$defaultNewOptionNm = t('Type an option here, then click add');

?>

<?php  if ($editMode) { ?>	

<h1><span><?php echo t('Edit Attribute Definition')?> (<em class="required">*</em> - <?php echo t('required field')?>)</span></h1>
<div class="ccm-dashboard-inner">
	<form method="post" id="ccm-attribute-update" action="<?php echo $this->url('/dashboard/pages/types/attributes/')?>" onsubmit="return ccmAttributesHelper.doSubmit">
	<?php echo $valt->output('add_or_update_attribute')?>
	<input type="hidden" name="akID" value="<?php echo $_REQUEST['akID']?>" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="update" value="1" />

	
	<?php 
	$attributeFormData=array(
			'akType'=>$akType,
			'akName'=>$akName,
			'akHandle'=>$akHandle, 
			'akValues'=>$akValues,	
			'akSearchable'=>$akSearchable, 
			'akAllowOtherValues'=>$akAllowOtherValues,
			'cancelURL'=>'/dashboard/pages/types',
			'defaultNewOptionNm'=>$defaultNewOptionNm,
			'formId'=>'ccm-attribute-update',
			'attributeType' => 'page',
			'submitBtnTxt'=>t('Update')
		);
	Loader::element('dashboard/attribute_form', $attributeFormData);
	?>
	
	<br>
	</form>	
	
</div>

<?php  

} else { ?>

<h1><span><?php echo t('Add Page Attribute')?></span></h1>
<div class="ccm-dashboard-inner">

<form method="post" id="ccm-add-attribute" action="<?php echo $this->url('/dashboard/pages/types/attributes/')?>" onsubmit="return ccmAttributesHelper.doSubmit">
<input type="hidden" name="add" value="1" />
<?php echo $valt->output('add_or_update_attribute')?>

	<?php 
	$attributeFormData=array(
			'akType'=>$_POST['akType'],
			'akName'=>$_POST['akName'],
			'akHandle'=>$_POST['akHandle'], 
			'akValues'=>$_POST['akValues'], 
			'akSearchable'=>$_POST['akSearchable'], 
			'akAllowOtherValues'=>$_POST['akAllowOtherValues'],
			'cancelURL'=>'/dashboard/pages/types',
			'defaultNewOptionNm'=>$defaultNewOptionNm,
			'formId'=>'ccm-add-attribute',
			'attributeType' => 'page',
			'submitBtnTxt'=>t('Add')
		);
	Loader::element('dashboard/attribute_form', $attributeFormData);
	?>

<br>
</form>	
</div>


<?php  } ?>