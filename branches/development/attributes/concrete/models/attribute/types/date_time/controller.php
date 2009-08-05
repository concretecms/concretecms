<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class DateTimeAttributeTypeController extends AttributeTypeController  {

	public $helpers = array('form');
	
	public function saveKey() {
		$ak = $this->getAttributeKey();
		
		$db = Loader::db();

		$akDateDisplayMode = $this->post('akDateDisplayMode');
				
		// now we have a collection attribute key object above.
		$db->Replace('atDateTimeSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akDateDisplayMode' => $akDateDisplayMode
		), array('akID'), true);
	}
	
	public function type_form() {
		$this->load();
	}
	
	public function form() {
		$dt = Loader::helper('form/date_time');
		print $dt->datetime($this->field('value'), $caValue);
	}

	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atDateTime where avID = ?", array($this->getAttributeValueID()));
		return $value;
	}
	
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		
		$db = Loader::db();
		$row = $db->GetRow('select akDateDisplayMode from atDateTimeSettings where akID = ?', $ak->getAttributeKeyID());
		$this->akDateDisplayMode = $row['akDateDisplayMode'];

		$this->set('akDateDisplayMode', $this->akDateDisplayMode);
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atDateTime where avID = ?', array($id));
		}
	}
	
	public function saveForm($data) {
		$db = Loader::db();
		$this->saveValue($data['value']);
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atDateTime where avID = ?', array($this->getAttributeValueID()));
	}

}