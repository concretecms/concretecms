<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('file_attributes');

class DashboardFilesAttributesController extends Controller {

	public function view() {
		$this->set( "attribs", FileAttributeKey::getList() );
		
		if ($_REQUEST['attribute_deleted']) {
			$message = t('File Attribute Deleted.');
		}elseif($_REQUEST['attribute_created']) {
			$message = t("File Attribute Key Created.");		
		}if($_REQUEST['attribute_updated']) {
			$message = t("File Attribute Key Updated.");		
		}
		$this->set( "message", $message );
		$this->set("showUserAdded", false);	
	}
	
	public function show_user_added() {
		$this->set( "attribs", FileAttributeKey::getUserAddedList() );
		$this->set("showUserAdded", true);
	}
	
	public function add(){
		$this->set( "pageMode", 'add' );
		
		if($_POST['submitted']) $this->save();		
	}	

	public function edit(){
		$fak=FileAttributeKey::get( intval($_REQUEST['fakID']) );
		if(!$fak)
			throw new Exception( t('No file attribute key specified') );
		$this->set( "fak", $fak );
		$this->set( "pageMode", 'edit' );
		
		if($_POST['submitted']) $this->save($fak);
	}
	
	private function save($fak=NULL){
		$txt = Loader::helper('text');
		$valt = Loader::helper('validation/token');
		
		$akHandle = $txt->sanitize($_POST['akHandle']);
		$akName = $txt->sanitize($_POST['akName']); 
		$akType = $txt->sanitize($_POST['akType']);
		$akSearchable = $_POST['akSearchable'] ? 1 : 0;
		
		//grab the attribute key possible values
		$akValuesArray=array(); 
		foreach($_POST as $key=>$newVal){ 
			if( !strstr($key,'akValue_') || $newVal=='TEMPLATE' ) continue; 
			$originalVal=$_REQUEST['akValueOriginal_'.str_replace('akValue_','',$key)];		
			$akValuesArray[]=$newVal; 
			//change all previous answers
			if($fak && $originalVal) $fak->renameValue($originalVal,$newVal);
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
		
		if (FileAttributeKey::inUse($akHandle)) {
			if ((!is_object($fak)) || ($fak->getAttributeKeyHandle() != $akHandle)) {
				$error[] = t("An attribute with the handle %s already exists.", $akHandle);
			}
		}
		
		if( count($error) == 0 ){
			if($fak && $_REQUEST['edit']){ 
				$fak = $fak->update($akHandle, $akName, $akValues, $akType); 
				$this->redirect('/dashboard/files/attributes/?attribute_updated=1');
			}elseif($_REQUEST['add']){
				$fak = FileAttributeKey::add($akHandle, $akName, $akValues, $akType, 0);
				$this->redirect('/dashboard/files/attributes/?attribute_created=1');				
			}
		}	
		
		$this->set( "error", $error );	
	}
	
	public function delete(){
		$valt = Loader::helper('validation/token');
		if ( $valt->validate('delete_attribute') ) { 
			$fa = FileAttributeKey::get( intval($_REQUEST['fakID']) );
			if (is_object($fa)) {
				$fa->delete();
				$this->redirect('/dashboard/files/attributes/?attribute_deleted=1');
			}
		}
		$this->redirect('/dashboard/files/attributes/');
	}
}

?>