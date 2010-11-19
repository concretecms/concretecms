<?php 
defined('C5_EXECUTE') or die("Access Denied.");

class ImageFileAttributeTypeController extends AttributeTypeController  {

	protected $searchIndexFieldDefinition = 'I DEFAULT 0 NULL';

	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select fID from atFile where avID = ?", array($this->getAttributeValueID()));
		if ($value > 0) {
			$f = File::getByID($value);
			return $f;
		}
	}
	
	public function getDisplayValue() {
		$f = $this->getValue();
		if (is_object($f)) {
			return '<a href="' . $f->getDownloadURL() . '">' . $f->getTitle() . '</a>';
		}
	}

	public function searchForm($list) {
		$fileID = $this->request('value');
		$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $fileID);
		return $list;
	}
	
	public function getSearchIndexValue() {
		$db = Loader::db();
		$value = $db->GetOne("select fID from atFile where avID = ?", array($this->getAttributeValueID()));
		return $value;	
	}
	
	public function search() {
		// search by file causes too many problems
		//$al = Loader::helper('concrete/asset_library');
		//print $al->file('ccm-file-akID-' . $this->attributeKey->getAttributeKeyID(), $this->field('value'), t('Choose File'), $bf);
	}
	
	public function form() {
		$bf = false;
		if ($this->getAttributeValueID() > 0) {
			$bf = $this->getValue();
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
		if ($data['value'] > 0) {
			$f = File::getByID($data['value']);
			$this->saveValue($f);
		} else {
			$db = Loader::db();
			$db->Replace('atFile', array('avID' => $this->getAttributeValueID(), 'fID' => 0), 'avID', true);	
		}
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atFile where avID = ?', array($this->getAttributeValueID()));
	}
	
}