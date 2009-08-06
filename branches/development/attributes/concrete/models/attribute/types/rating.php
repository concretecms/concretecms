<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class RatingAttributeTypeController extends AttributeTypeController  {

	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atNumber where avID = ?", array($this->getAttributeValueID()));
		return round($value);	
	}

	public function form() {
		$caValue = 0;
		if ($this->getAttributeValueID() > 0) {
			$caValue = $this->getValue();
		}
		$rt = Loader::helper('form/rating');
		print $rt->rating($this->field('value'), $caValue);
	}

	// run when we call setAttribute(), instead of saving through the UI
	public function saveValue($rating) {
		$db = Loader::db();
		$db->Replace('atNumber', array('avID' => $this->getAttributeValueID(), 'value' => $rating), 'avID', true);
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atNumber where avID = ?', array($id));
		}
	}
	
	public function saveForm($data) {
		$this->saveValue($data['value']);
	}
	
	public function search() {
		$this->form();
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atNumber where avID = ?', array($this->getAttributeValueID()));
	}
	
}