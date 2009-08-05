<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class ImageFileAttributeTypeController extends AttributeTypeController  {

	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select fID from atFile where avID = ?", array($this->getAttributeValueID()));
		return $value;	
	}

	public function form() {
		$bf = false;
		if ($this->getAttributeValueID() > 0) {
			$bf = File::getByID($this->getValue());
		}
		$al = Loader::helper('concrete/asset_library');
		print $al->file('ccm-file-akID-' . $this->attributeKey->getAttributeKeyID(), $this->field('value'), t('Choose File'), $bf);
	}

	// run when we call setAttribute(), instead of saving through the UI
	public function saveValue($obj) {
		$db = Loader::db();
		$db->Replace('atFile', array('avID' => $this->getAttributeValueID(), 'fID' => $obj->getFileID()), 'avID', true);
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atFile where avID = ?', array($id));
		}
	}
	
	public function saveForm($data) {
		$f = File::getByID($data['value']);
		$this->saveValue($f);
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atFile where avID = ?', array($this->getAttributeValueID()));
	}
	
}