<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class SelectAttributeTypeController extends AttributeTypeController  {

	public function type_form() {
		$this->set('derp', 'lkjfs');
		$path1 = $this->getView()->getAttributeTypeURL('type_form.js');
		$path2 = $this->getView()->getAttributeTypeURL('type_form.css');
		$this->addHeaderItem(Loader::helper('html')->javascript($path1));
		$this->addHeaderItem(Loader::helper('html')->css($path2));
		$this->set('form', Loader::helper('form'));
		
		$akSelectValues = $this->getSelectValuesFromPost();

		$this->set('akSelectValues', $akSelectValues);
	}
	
	private function getSelectValuesFromPost() {
		$akSelectValuesArray=array(); 
		foreach($_POST as $key=>$newVal) { 
			if( !strstr($key,'akSelectValue_') || $newVal=='TEMPLATE' ) continue; 
			$originalVal=$_REQUEST['akSelectValueOriginal_'.str_replace('akSelectValue_','',$key)];		
			$akSelectValuesArray[]=$newVal; 
			//change all previous answers
			if($ak) $ak->renameValue($originalVal,$newVal);
		}
		$akSelectValuesArray=array_unique($akSelectValuesArray);
		return $akSelectValuesArray;
	}
	
	public function add() {
		parent::add();
		$e = $this->get("attributeError");
		
		// additional validation for select type
		
		if (!$e->has()) {
			$vals = $this->getSelectValuesFromPost();
			if (count($vals) < 2 && $this->post('akSelectAllowOtherValues') == 0) {
				$e->add(t('A select attribute type must have at least two values, or must allow users to add to it.'));
			}
		}
		
		if (!$e->has()) {
			$ak = CollectionAttributeKey::add($this->post('akHandle'), $this->post('akName'), $this->post('akIsSearchable'), $this->post('atID'), $this->post('akSelectAllowMultipleValues'), $this->post('akSelectAllowOtherValues'), $vals);		
			print_r($ak);
		}

		$this->set('error', $e);
	}
	
}