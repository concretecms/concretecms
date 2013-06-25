<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_AttributeType_Boolean extends AttributeTypeController  {
	
	// Field definition in the ADODB Format. We omit the first column (name) though, since it's
	// automatically generated
	
	protected $searchIndexFieldDefinition = 'I1 DEFAULT 0 NULL';
	
	public function searchForm($list) {
		$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), 1);
		return $list;
	}	

	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atBoolean where avID = ?", array($this->getAttributeValueID()));
		return $value;	
	}
	
	public function exportKey($akey) {
		$this->load();
		$type = $akey->addChild('type');
		$type->addAttribute('checked', $this->akCheckedByDefault);
		return $akey;
	}
	
	public function importKey($akey) {
		if (isset($akey->type)) {
			$data['akCheckedByDefault'] = $akey->type['checked'];
			$this->saveKey($data);
		}
	}
	
	public function getDisplayValue() {
		$v = $this->getValue();
		return ($v == 1) ? t('Yes') : t('No');
	}

	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		
		$db = Loader::db();
		$row = $db->GetRow('select akCheckedByDefault from atBooleanSettings where akID = ?', $ak->getAttributeKeyID());
		$this->akCheckedByDefault = $row['akCheckedByDefault'];
		$this->set('akCheckedByDefault', $this->akCheckedByDefault);
	}

	public function form() {

		if (is_object($this->attributeValue)) {
			$value = $this->getAttributeValue()->getValue();
			$checked = $value == 1 ? true : false;
		} else {
			$this->load();
			if ($this->akCheckedByDefault) {
				$checked = true;
			}
		}
		
		$cb = Loader::helper('form')->checkbox($this->field('value'), 1, $checked);
		print '<label class="checkbox">' . $cb . ' <span>' . t('Yes') . '</span></label>';
	}
	
	public function composer() {
		print '<label class="checkbox">';
		$this->form();
		print '</label>';
	}
	
	public function search() {
		print '<label class="checkbox">' . Loader::helper('form')->checkbox($this->field('value'), 1, $this->request('value') == 1) . ' ' . t('Yes') . '</label>';
	}

	public function type_form() {
		$this->set('form', Loader::helper('form'));	
		$this->load();
	}
	
	// run when we call setAttribute(), instead of saving through the UI
	public function saveValue($value) {
		$db = Loader::db();
		$value = ($value == false || $value == '0') ? 0 : 1;
		$db->Replace('atBoolean', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$db->Execute('delete from atBooleanSettings where akID = ?', array($this->getAttributeKey()->getAttributeKeyID()));

		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atBoolean where avID = ?', array($id));
		}
	}
	
	public function duplicateKey($newAK) {
		$this->load();
		$db = Loader::db();
		$db->Execute('insert into atBooleanSettings (akID, akCheckedByDefault) values (?, ?)', array($newAK->getAttributeKeyID(), $this->akCheckedByDefault));	
	}
	
	public function saveKey($data) {
		$ak = $this->getAttributeKey();
		$db = Loader::db();
		$akCheckedByDefault = $data['akCheckedByDefault'];
		
		if ($data['akCheckedByDefault'] != 1) {
			$akCheckedByDefault = 0;
		}

		$db->Replace('atBooleanSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akCheckedByDefault' => $akCheckedByDefault
		), array('akID'), true);
	}
	
	public function saveForm($data) {
		$db = Loader::db();
		$this->saveValue($data['value']);
	}
	
	// if this gets run we assume we need it to be validated/checked
	public function validateForm($data) {
		return $data['value'] == 1;
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atBoolean where avID = ?', array($this->getAttributeValueID()));
	}
	
}