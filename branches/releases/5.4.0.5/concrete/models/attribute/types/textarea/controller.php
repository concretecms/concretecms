<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('attribute/types/default/controller');

class TextareaAttributeTypeController extends DefaultAttributeTypeController  {
	
	public $helpers = array('form');
	
	public function saveKey($data) {
		$akTextareaDisplayMode = $data['akTextareaDisplayMode'];
		if (!$akTextareaDisplayMode) {
			$akTextareaDisplayMode = 'text';
		}
		$this->setDisplayMode($akTextareaDisplayMode);
	}
	
	public function form() {
		$this->load();
		if (is_object($this->attributeValue)) {
			$value = $this->getAttributeValue()->getValue();
		}
		// switch display type here
		switch($this->akTextareaDisplayMode) {
			case "rich_text":
				Loader::element('editor_init');
				Loader::element('editor_config');
				Loader::element('editor_controls', array('mode'=>'full'));
				print Loader::helper('form')->textarea($this->field('value'), $value, array('class' => 'ccm-advanced-editor'));
			break;
			case "text":
			default:
				print Loader::helper('form')->textarea($this->field('value'), $value);
			break;
		}
	}

	public function searchForm($list) {
		$db = Loader::db();
		$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), '%' . $this->request('value') . '%', 'like');
		return $list;
	}
	
	public function search() {
		$f = Loader::helper('form');
		print $f->text($this->field('value'), $this->request('value'));
	}
	

	public function setDisplayMode($akTextareaDisplayMode) {
		$db = Loader::db();
		$ak = $this->getAttributeKey();
		$db->Replace('atTextareaSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akTextareaDisplayMode' => $akTextareaDisplayMode
		), array('akID'), true);
	}
	
	/* 
	public function saveForm($data) {
		$db = Loader::db();
		$this->saveValue($data['value']);
	}
	*/
	
	// should have to delete the at thing
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atDefault where avID = ?', array($id));
		}
		
		$db->Execute('delete from atTextareaSettings where akID = ?', array($this->attributeKey->getAttributeKeyID()));
	}
	
	public function type_form() {
		$this->load();
	}
	
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		
		$db = Loader::db();
		$row = $db->GetRow('select akTextareaDisplayMode from atTextareaSettings where akID = ?', $ak->getAttributeKeyID());
		$this->akTextareaDisplayMode = $row['akTextareaDisplayMode'];
		$this->set('akTextareaDisplayMode', $this->akTextareaDisplayMode);
	}
	
	public function duplicateKey($newAK) {
		$this->load();
		$db = Loader::db();
		$db->Replace('atTextareaSettings', array(
			'akID' => $newAK->getAttributeKeyID(), 
			'akTextareaDisplayMode' => $this->akDateDisplayMode
		), array('akID'), true);
	}
}
