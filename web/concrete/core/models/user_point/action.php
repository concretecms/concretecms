<?php defined('C5_EXECUTE') or die(_("Access Denied.")); 

/**
 * @author Ryan Tyler ryan@concrete5.org
 */
class Concrete5_Model_UserPointAction extends Model {
	public $_table = 'UserPointActions';
	
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
		$row = $db->getRow("SELECT * FROM UserPointActions WHERE upaID = ?",array($upaID));
		if ($row['upaID']) {
			$class = 'UserPointAction';
			if ($row['upaHasCustomClass']) {
				$pkgHandle = false;
				if ($row['pkgID']) {
					$pkgHandle = PackageList::getHandle($row['pkgID']);
				}
				Loader::model('user_point/actions/' . $row['upaHandle'], $pkgHandle);
				$class = Loader::helper('text')->camelcase($row['upaHandle']) . $class;
			}
			$upa = new $class();
			$upa->setDataFromArray($row);
			return $upa;
		}
	}


	/** 
	 * @param Package $pkg
	 * @return array
 	 */
	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$upaIDs = $db->GetCol('select upaID from UserPointActions where pkgID = ? order by upaName asc', array($pkg->getPackageID()));
		$actions = array();
		foreach($upaIDs as $upaID) {
			$action = UserPointAction::getByID($upaID);
			if (is_object($action)) {
				$actions[] = $action;
			}
		}
		return $actions;
	}
	
	/**
	 * @param $upaHandle
	 * @return UserPointAction
	*/
	public static function getByHandle($upaHandle) {
		$db = Loader::db();
		$row = $db->getRow("SELECT * FROM UserPointActions WHERE upaHandle = ?",array($upaHandle));
		if ($row['upaID']) {
			$class = 'UserPointAction';
			if ($row['upaHasCustomClass']) {
				$pkgHandle = false;
				if ($row['pkgID']) {
					$pkgHandle = PackageList::getHandle($row['pkgID']);
				}
				Loader::model('user_point/actions/' . $row['upaHandle'], $pkgHandle);
				$class = Loader::helper('text')->camelcase($row['upaHandle']) . $class;
			}
			$upa = new $class();
			$upa->setDataFromArray($row);
			return $upa;
		}
	}

	public static function add($upaHandle, $upaName, $upaDefaultPoints, $group, $pkg = false) {
		$upa = new UserPointAction();
		$upa->upaHandle = $upaHandle;
		$upa->upaName = $upaName;
		$upa->upaDefaultPoints = $upaDefaultPoints;
		$upa->gBadgeID = 0;
		$upa->upaHasCustomClass = 0;
		if (is_object($group)){
			$upa->gBadgeID = $group->getGroupID();
		}
		$upa->pkgID = 0;
		$pkgHandle = false;
		if (is_object($pkg)) {
			$upa->pkgID = $pkg->getPackageID();
			$pkgHandle = $pkg->getPackageHandle();
		}

		$env = Environment::get();
		$r = $env->getRecord(DIRNAME_MODELS . '/' . DIRNAME_USER_POINTS . '/' . DIRNAME_ACTIONS . '/' . $upaHandle . '.php', $pkgHandle);
		if ($r->exists()) {
			$upa->upaHasCustomClass = 1;
		}

		$upa->save();
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
			$this->upaIsActive = $data['upaIsActive'];
			return true;
		} else {
			return false;
		}
	}
	
	/** 
	 * @return boolean
	 */
	public function hasCustomClass() {
		return $this->upaHasCustomClass;
	}

	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}

	public function getPackageID() {
		return $this->pkgID;
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

	public function isUserPointActionActive() {
		return $this->upaIsActive;
	}

	/**
	 * @return Group
	*/
	public function getUserPointActionBadgeGroupObject() {
		return Group::getByID($this->getUserPointActionBadgeGroupID());
	}
	
	public function addDetailedEntry($user, UserPointActionDescription $descr, $points = false, $date = null) {
		$this->addEntry($user, $descr, $points, $date);
	}

	public function addEntry($user, UserPointActionDescription $descr, $points = false, $date = null) {

		if (!$this->isUserPointActionActive()) {
			return false;
		}

		if(is_object($user)) {
			$user = UserInfo::getByID($user->getUserID());
			$uID = $user->getUserID();
		} else {
			$uID = $user;
		}
		
		if(!isset($uID) || $uID <= 0) {
			return false;
		}
	
		$g = $this->getUserPointActionBadgeGroupObject();
		if($g instanceof Group) {
			$user->enterGroup($g);
		}
		
		if ($date == null) {
			$date = date('Y-m-d H:i:s');
		}
		
		if($points === false) {
			$points = $this->getUserPointActionDefaultPoints();
		}

		try {
			$upe = new UserPointEntry();
			$upe->upuID = $uID;
			$upe->upaID = $this->upaID;
			$upe->upPoints = $points;
			$upe->timestamp = $date;
			$descr = serialize($descr);
			$upe->object = $descr;
			$upe->save();
			return $upe;
		} catch(Exception $e) {
			Log::addEntry("Error saving user point record: ".$e->getMessage(),'exceptions');
			return false;
		}
		
		return true;
	}
		
}