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
	public function setValue($value) {
		
	}
	
	public function save($data) {
		$db = Loader::db();
		$db->Replace('atDefault', array('avID' => $this->getAttributeValueID(), 'value' => $data['value']), 'avID', true);
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from atDefault where avID = ?', array($this->getAttributeValueID()));
	}
	
}
