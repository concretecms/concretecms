<?php defined('C5_EXECUTE') or die(_("Access Denied.")); 
class Concrete5_Model_UserPointEntry extends Model {
	public $_table = 'UserPointHistory'; 
	
	public $upID;
	public $upuID;
	public $upaID = 0;
	public $upPoints;
	public $timestamp;
	
	public function load($upID) {
		$db = Loader::db();
		parent::load('upID='.$db->quote($upID));
	}
	
	public function	getUserPointEntryID() {
		return $this->upID;
	}
	
	public function	getUserPointEntryActionID() {
		return $this->upaID;
	}
	
	public function	getUserPointEntryActionObject() {
		return UserPointAction::getByID($this->getUserPointEntryActionID());
	}
	
	public function	getUserPointEntryValue() {
		return $this->upPoints;
	}	
	
	public function	getUserPointEntryTimestamp() {
		return $this->timestamp;
	} // - returns unix timestamp stored in the timestamp column
	
	public function	getUserPointEntryDateTime() {
		return $this->timestamp;
	} // - returns it in a nicely formatted way
	
	public function getUserPointEntryUserID() {
		return $this->upuID;
	}

	public function getUserPointEntryDescription() {
		if ($this->object) {
			$obj = unserialize($this->object);
			return $obj->getUserPointActionDescription();
		}
	}

	public function getUserPointEntryUserObject() {
		$ui = UserInfo::getByID($this->upuID);
		return $ui;
	}
	
	public static function getTotal($ui) {
		$db = Loader::db();
		$cnt = $db->GetOne('select sum(upPoints) as total from UserPointHistory where upuID = ?', array($ui->getUserID()));
		return $cnt;
	}

}
