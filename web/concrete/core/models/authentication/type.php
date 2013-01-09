<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AuthenticationType extends Object {

	public function getAuthenticationTypeID() {return $this->authTypeID;}
	public function getAuthenticationTypeHandle() {return $this->authTypeHandle;}
	public function getAuthenticationTypeName() {return $this->authTypeName;}
	public function getAuthenticationTypeStatus() {return $this->authTypeIsEnabled;}
	public function getAuthenticationTypeDisplayOrder() {return $this->authTypeDisplayOrder;}
	public function getAuthenticationTypePackageID() {return $this->pkgID;}
	public function isEnabled() {return !!$this->getAuthenticationTypeStatus();}
	public function getController() {return $this->controller;}
	
	public static function getByID($authTypeID) {
		$db = Loader::db();
		$row = $db->GetRow('SELECT * FROM AuthenticationTypes where authTypeID=?', array($authTypeID));
		if (!$row) {
			throw new Exception('Invalid Authentication Type ID');
		}
		$at = AuthenticationType::load($row);
		$at->loadController();
		return $at;
	}
	
	public static function load($arr) {
		$extract = array('authTypeID','authTypeName','authTypeHandle','authTypeHandle','authTypeName','authTypeDisplayOrder','pkgID');
		$obj = new AuthenticationType;
		foreach ($extract as $key) {
			if (!isset($arr[$key])) {
				return false;
			}
			$obj->{$key} = $arr[$key];
		}
		$obj->loadController();
		return $obj;
	}

	public static function getList() {
		$list = array();
		$db = Loader::db();
		$q = $db->query("SELECT * FROM AuthenticationTypes");
		while ($row = $q->fetchRow()) {
			$list[] = AuthenticationType::load($row);
		}
		return $list;
	}

	public static function getActiveListSorted() {
		$list = array();
		$db = Loader::db();
		$q = $db->query("SELECT * FROM AuthenticationTypes WHERE authTypeIsEnabled=1 ORDER BY authTypeDisplayOrder");
		while ($row = $q->fetchRow()) {
			$list[] = AuthenticationType::load($row);
		}
		return $list;
	}

	public function disable() {
		$db = Loader::db();
		$db->Execute('UPDATE AuthenticationTypes SET authTypeHandle=0');
	}

	public function enable() {
		$db = Loader::db();
		$db->Execute('UPDATE AuthenticationTypes SET authTypeHandle=1');
	}

	public function toggle() {
		return ($this->isEnabled() ? $this->disable() : $this->enable());
	}

	public function delete() {
		$db = Loader::db();
		if (method_exists($this->controller, 'deleteType')) {
			$this->controller->deleteType();
		}
		
		$db->Execute("DELETE FROM AuthenticationTypes WHERE authTypeID=?", array($this->authTypeID));
	}
	
	public static function getListByPackage(Package $pkg) {
		$db = Loader::db();
		$list = array();

		$q = $db->query('SELECT * FROM AuthenticationTypes WHERE pkgID=?', array($pkg->getPackageID()));
		while ($row = $q->FetchRow()) {
			$list[] = AuthenticationType::load($row);
		}
		$r->Close();
		return $list;
	}

	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}

	public static function getByHandle($atHandle) {
		$db = Loader::db();
		$row = $db->GetRow('SELECT * FROM AuthenticationTypes WHERE authTypeHandle=?', array($atHandle));
		if (!$row) {
			throw new Exception('Invalid Authentication Type Handle');
		}
		$at = AuthenticationType::load($row);
		return $at;
	}	

	public static function add($atHandle, $atName, $order=0, $pkg=false) {
		$die = true;
		try {
			AuthenticationType::getByHandle($atHandle);
		} catch (exception $e) {
			$die = false;
		}
		if ($die) {
			throw new exception('Authentication type with handle "'.$atHandle.'" already exists!');
		}

		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('INSERT INTO AuthenticationTypes (authTypeHandle, authTypeName, authTypeIsEnabled, authTypeDisplayOrder, pkgID) values (?, ?, ?, ?, ?)',
			array($atHandle, $atName, 1, intval($order), $pkgID));
		$id = $db->Insert_ID();
		$est = AuthenticationType::getByID($id);
		$path = $est->mapAuthenticationTypeFilePath(FILENAME_AUTHENTICATION_DB);
		if ($path) {
			Package::installDB($path);
		}

		return $est;
	}
	
	public function getAuthenticationTypeIconSRC() {
		$ff = '/'.FILENAME_BLOCK_ICON;
		if ($this->getPackageID() > 0) {
			$db = Loader::db();
			$h = $this->getPackageHandle();
			$url = (is_dir(DIR_PACKAGES.'/'.$h)) ? BASE_URL.DIR_REL : ASSETS_URL; 
			$url = $url.'/'.DIRNAME_PACKAGES.'/'.$h.'/'.DIRNAME_MODELS.'/'.DIRNAME_AUTHENTICATION.'/'.DIRNAME_AUTHENTICATION_TYPES.'/'.$this->getAuthenticationTypeHandle().$ff;
		} else if (file_exists(DIR_MODELS_CORE.'/'.DIRNAME_AUTHENTICATION.'/'. DIRNAME_AUTHENTICATION_TYPES.'/'.$this->getAuthenticationTypeHandle().$ff)) {
			$url = ASSETS_URL.'/'.DIRNAME_MODELS.'/'.DIRNAME_AUTHENTICATION.'/'. DIRNAME_AUTHENTICATION_TYPES.'/'.$this->getAuthenticationTypeHandle().$ff;
		} else if (file_exists(DIR_MODELS.'/'.DIRNAME_AUTHENTICATION.'/'. DIRNAME_AUTHENTICATION_TYPES.'/'.$this->getAuthenticationTypeHandle().$ff)) {
			$url = BASE_URL.DIR_REL.'/'.DIRNAME_MODELS.'/'.DIRNAME_AUTHENTICATION.'/'. DIRNAME_AUTHENTICATION_TYPES.'/'.$this->getAuthenticationTypeHandle().$ff;
		} else {
			$url = ASSETS_URL.'/'.DIRNAME_MODELS.'/'.DIRNAME_AUTHENTICATION.'/'. DIRNAME_AUTHENTICATION_TYPES.'/default'.$ff;		
		}
		return $url;
	}
	
	public function getAuthenticationTypeFilePath($_file) {
		$f = $this->mapAuthenticationTypeFilePath($_file);
		if ($f) {
			return BASE_URL.DIR_REL.$f;
		}
		return false;
	}

	protected function mapAuthenticationTypeFilePath($_file) {
		$atHandle = $this->getAuthenticationTypeHandle();

		$locations = array();
		$locations[] = implode('/',array(DIR_MODELS,DIRNAME_AUTHENTICATION,DIRNAME_AUTHENTICATION_TYPES,$atHandle,$_file));
		if ($_file == FILENAME_AUTHENTICATION_CONTROLLER) {
			$locations[] = implode('/',array(DIR_MODELS,DIRNAME_AUTHENTICATION,DIRNAME_AUTHENTICATION_TYPES,$atHandle)).".php";
		}

		if ($this->pkgID > 0) {
			$pkgHandle = PackageList::getHandle($pkgID);
			$dirp = is_dir(DIR_PACKAGES.'/'.$pkgHandle)?DIR_PACKAGES.'/'.$pkgHandle:DIR_PACKAGES_CORE.'/'.$pkgHandle;
			$locations[] = implode('/',array($dirp,DIRNAME_MODELS,DIRNAME_AUTHENTICATION,DIRNAME_AUTHENTICATION_TYPES,$atHandle,$_file));
			if ($_file == FILENAME_AUTHENTICATION_CONTROLLER) {
				$locations[] = implode('/',array($dirp,DIRNAME_MODELS,DIRNAME_AUTHENTICATION,DIRNAME_AUTHENTICATION_TYPES,$atHandle)).".php";
			}
		}

		$locations[] = implode('/',array(DIR_MODELS_CORE,DIRNAME_AUTHENTICATION,DIRNAME_AUTHENTICATION_TYPES,$atHandle,$_file));
		if ($_file == FILENAME_AUTHENTICATION_CONTROLLER) {
			$locations[] = implode('/',array(DIR_MODELS_CORE,DIRNAME_AUTHENTICATION,DIRNAME_AUTHENTICATION_TYPES,$atHandle)).".php";
		}

		foreach($locations as $location) {
			if (file_exists($location)) {
				return $location;
			}
		}
		return false;
	}
	
	protected function loadController() { 
		// local scope
		$atHandle = $this->authTypeHandle;
		$txt = Loader::helper('text');
		$className = $txt->camelcase($this->authTypeHandle).'AuthenticationTypeController';
		$file = $this->mapAuthenticationTypeFilePath(FILENAME_AUTHENTICATION_CONTROLLER);
		if (!$file) {
			throw new exception('Authentication type controller "'.$className.'" does not exist.');
		}
		require_once($file);
		$this->controller = new $className($this);
	}
	
}

