<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_AttributeType_Rating extends AttributeTypeController  {

	protected $searchIndexFieldDefinition = 'N 14.4 NULL';

	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atNumber where avID = ?", array($this->getAttributeValueID()));
		return round($value);
	}
	
	public function getDisplayValue() {
		$value = $this->getValue();
		$rt = Loader::helper('rating');
		return $rt->output($akHandle . time(), $value);
	}

	public function form() {
		$caValue = 0;
		if ($this->getAttributeValueID() > 0) {
			$caValue = $this->getValue();
		}
		$rt = Loader::helper('form/rating');
		print $rt->rating($this->field('value'), $caValue);
	}

	public function searchForm($list) {
		$minRating = $this->request('value');
		$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $minRating, '>=');
		return $list;
	}
	
	// run when we call setAttribute(), instead of saving through the UI
	public function saveValue($rating) {
		if ($rating == '') {
			$rating = 0;
		}
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
		$rt = Loader::helper('form/rating');
		print $rt->rating($this->field('value'), $this->request('value'), false);
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atNumber where avID = ?', array($this->getAttributeValueID()));
	}
	
}