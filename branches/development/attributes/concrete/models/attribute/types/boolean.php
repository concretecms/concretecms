<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class BooleanAttributeTypeController extends AttributeTypeController  {

	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atBoolean where avID = ?", array($this->getAttributeValueID()));
		return $value;	
	}

	public function form() {

		if (is_object($this->attributeValue)) {
			$value = $this->getAttributeValue()->getValue();
		}

		$checked = $value == 1 ? true : false;
		$cb = Loader::helper('form')->checkbox($this->field('value'), 1, $checked);
		print $cb . ' ' . t('Yes');
	}

	// run when we call setAttribute(), instead of saving through the UI
	public function saveValue($value) {
		$db = Loader::db();
		$value = ($value == false || $value == '0') ? 0 : 1;
		$db->Replace('atBoolean', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
	}
	
	public function saveForm($data) {
		$db = Loader::db();
		$this->saveValue($data['value']);
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from atBoolean where avID = ?', array($this->getAttributeValueID()));
	}
	
}