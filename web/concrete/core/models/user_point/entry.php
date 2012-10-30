<?php defined('C5_EXECUTE') or die(_("Access Denied.")); 
class Concrete5_Model_UserPointEntry extends Model {
	public $_table = 'UserPointHistory'; 
	
	public $upID;
	public $upuID;
	public $upaID = 0;
	public $upPoints;
	public $upComments;
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
	
	public function	getUserPointEntryComments() {
		$comments = $this->upComments;
		return $comments;
	}
	
	public function	getUserPointEntryTimestamp() {
		return $this->timestamp;
	} // - returns unix timestamp stored in the timestamp column
	
	public function	getUserPointEntryDateTime() {
		return $this->timestamp;
	} // - returns it in a nicely formatted way
	
	public function getUserPointEntryUserObject() {
		return User::getByUserID($this->getUserPointEntryUserID());
	}
	
	public function getUserPointEntryUserID() {
		return $this->upuID;
	}
	
	public function getUserPointEntryRelatedText() {
		$somethingID = $this->getUserPointEntryRelatedID();
		$entry_text = Loader::helper('user_point/entry_text');
		$ka = $this->getUserPointEntryActionObject();
		return $entry_text->getRelatedText($ka->getUserPointActionHandle(),$this->getUserPointEntryRelatedID());
	}
	
	
	
	/**
	 * adds a karm point entry for a user
	 * @param DiscussionUser | UserInfo | User | int $user 
	 * @param UserPointAction | string $actionObj
	 * @param string $comments 
	 * @param int $points override the number of points. By default we use the points bound to the $actionObj action
	 * @param mixed $relatedID
	 * @return boolean
	 */
	public static function add($user, $actionObj, $comments = false, $points = false, $relatedID = NULL, $date = null) {
		if(is_object($user)) {
			$user = UserInfo::getByID($user->getUserID());
			$uID = $user->getUserID();
		} else {
			$uID = $user;
		}
		
		if(!isset($uID) || $uID <= 0) {
			return false;
		}
	
		if(!($actionObj instanceof UserPointAction)) {
			if(is_string($actionObj)) {
				$actionObj = UserPointAction::getByHandle($actionObj);
				$upaID = $actionObj->getUserPointActionID();
			} elseif(is_numeric($actionObj)) { 
				$actionObj = UserPointAction::getByID($actionObj);
				$upaID = $actionObj->getUserPointActionID();
			} 
		} else {
			$upaID = $actionObj->getUserPointActionID();
		}
	
		$g = $actionObj->getUserPointActionBadgeGroupObject();
		if($g instanceof Group) {
			$user->enterGroup($g);
		}
		
		if ($date == null) {
			$date = date('Y-m-d H:i:s');
		}
		
		if(!$comments) {
			$comments = "";
		}
		
		if(!strlen($relatedID)) {
			$relatedID = "";
		}
		
		if($points === false) {
			$points = $actionObj->getUserPointActionDefaultPoints();
		}
		
		try {
			$upe = new UserPointEntry();
			$upe->upuID = $uID;
			$upe->upaID = $upaID;
			$upe->upPoints = $points;
			$upe->upComments = $comments;
			$upe->upRelatedID = $relatedID; 
			$upe->timestamp = $date;
			$upe->save();
		} catch(Exception $e) {
			Log::addEntry("Error saving user point record: ".$e->getMessage(),'exceptions');
			return false;
		}
		
		return true;
	}
	

	public function save() {
		$dt = Loader::helper('date');
		if(!isset($this->timestamp)) {
			$this->timestamp = $dt->getSystemDateTime(); 
		}
		parent::save();
	}
	
	


}
