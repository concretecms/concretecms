<?php defined('C5_EXECUTE') or die(_("Access Denied.")); 

/**
 * @author Ryan Tyler ryan@concrete5.org
 */
class Concrete5_Model_UserPointAction extends Model {
	public $_table = 'UserPointActions';
	
	const TYPE_HELP = 1;
	//const TYPE_PROMOTION = 2;
	//const TYPE_WORK = 3;

	public $upaID;
	public $upaHandle;
	public $upaName;
	public $upaDefaultPoints;
	public $gBadgeID;
	
	
	public function load($upaID) {
		$db = Loader::db();
		parent::load('upaID='.$db->quote($upaID));
	}
	
	/**
	 * @param $upaID
	 * @return UserPointAction
	 */
	public static function getByID($upaID) {
		$db = Loader::db();
		$upa = new UserPointAction();
		$row = $db->getRow("SELECT * FROM UserPointActions WHERE upaID = ?",array($upaID));
		$upa->setDataFromArray($row);
		if($upa->upaID <= 0) {
			$upa = false;
		}
		return $upa;
	}
	
	/**
	 * @param $upaHandle
	 * @return UserPointAction
	*/
	public static function getByHandle($upaHandle) {
		$db = Loader::db();
		$upa = new UserPointAction();
		$row = $db->getRow("SELECT * FROM UserPointActions WHERE upaHandle = ?",array($upaHandle));
		$upa->setDataFromArray($row);
		if($upa->upaID <= 0) {
			$upa = false;
		}
		return $upa;
	}

	/**
	 * @param array $data
	 * @return boolean
	 */
	protected function setDataFromArray($data) {
		if(is_array($data) && count($data)) {
			$this->upaID = $data['upaID'];
			$this->upaHandle = $data['upaHandle'];
			$this->upaName = $data['upaName'];
			$this->upaDefaultPoints = $data['upaDefaultPoints'];
			$this->gBadgeID = $data['gBadgeID'];
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * @return string
	*/
	public function getUserPointActionHandle() {
		return $this->upaHandle;
	}
	
	/**
	 * @return string
	*/
	public function getUserPointActionName() {
		return $this->upaName;
	}
	
	/**
	 * @return int
	 */
	public function getUserPointActionID() {
		return $this->upaID;
	}
	
	/**
	 * @return int
	 */
	public function getUserPointActionDefaultPoints() {
		return $this->upaDefaultPoints;
	}
	
	/**
	 * @return int
	 */
	public function getUserPointActionBadgeGroupID() {
		return $this->gBadgeID;
	}
	
	/**
	 * @return Group
	*/
	public function getUserPointActionBadgeGroupObject() {
		return Group::getByID($this->getUserPointActionBadgeGroupID());
	}
	

		
}