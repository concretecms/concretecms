<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class DefaultAttributeTypeController extends AttributeTypeController  {

	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atDefault where avID = ?", array($this->getAttributeValueID()));
		return $value;	
	}

	public function form() {
		if (is_object($this->attributeValue)) {
			$value = $this->getAttributeValue()->getValue();
		}
		print '<textarea name="' . $this->field('value') . '" style="width: 100%; height: 40px">' . $value . '</textarea>';
	}

	// run when we call setAttribute(), instead of saving through the UI
	public function saveValue($value) {
		$db = Loader::db();
		$db->Replace('atDefault', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
	}
	
	public function saveForm($data) {
		$db = Loader::db();
		$this->saveValue($data['value']);
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from atDefault where avID = ?', array($this->getAttributeValueID()));
	}
	
}
