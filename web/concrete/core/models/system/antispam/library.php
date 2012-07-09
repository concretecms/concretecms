<?

class Concrete5_Model_SystemAntispamLibrary extends Object {

	public function getSystemAntispamLibraryHandle() { return $this->saslHandle;}
	public function getSystemAntispamLibraryName() { return $this->saslName;}
	public function isSystemAntispamLibraryActive() { return $this->saslIsActive;}
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	public function getPackageObject() {return Package::getByID($this->pkgID);}
	
	public static function getActive() {
		$db = Loader::db();
		$saslHandle = $db->GetOne('select saslHandle from SystemAntispamLibraries where saslIsActive = 1');
		if ($saslHandle) { 
			return SystemAntispamLibrary::getByHandle($saslHandle);
		}
	}
	
	public static function getByHandle($saslHandle) {
		$db = Loader::db();
		$r = $db->GetRow('select saslHandle, saslIsActive, pkgID, saslName from SystemAntispamLibraries where saslHandle = ?', array($saslHandle));
		if (is_array($r) && $r['saslHandle']) {
			$sc = new SystemAntispamLibrary();
			$sc->setPropertiesFromArray($r);
			return $sc;
		}
	}
	
	public static function add($saslHandle, $saslName, $pkg = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('insert into SystemAntispamLibraries (saslHandle, saslName, pkgID) values (?, ?, ?)', array($saslHandle, $saslName, $pkgID));
		return SystemAntispamLibrary::getByHandle($saslHandle);
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from SystemAntispamLibraries where saslHandle = ?', array($this->saslHandle));
	}
	
	public function activate() {
		$db = Loader::db();
		self::deactivateAll();
		$db->Execute('update SystemAntispamLibraries set saslIsActive = 1 where saslHandle = ?', array($this->saslHandle));
	}
	
	public static function deactivateAll() {
		$db = Loader::db();
		$db->Execute('update SystemAntispamLibraries set saslIsActive = 0');
	}
		
	public static function getList() {
		$db = Loader::db();
		$saslHandles = $db->GetCol('select saslHandle from SystemAntispamLibraries order by saslHandle asc');
		$libraries = array();
		foreach($saslHandles as $saslHandle) {
			$sasl = SystemAntispamLibrary::getByHandle($saslHandle);
			$libraries[] = $sasl;
		}
		return $libraries;
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$saslHandles = $db->GetCol('select saslHandle from SystemAntispamLibraries where pkgID = ? order by saslHandle asc', array($pkg->getPackageID()));
		$libraries = array();
		foreach($saslHandles as $saslHandle) {
			$sasl = SystemAntispamLibrary::getByHandle($saslHandle);
			$libraries[] = $sasl;
		}
		return $libraries;
	}
	
	public static function exportList($xml) {
		$list = self::getList();
		$nxml = $xml->addChild('systemantispam');
		
		foreach($list as $sc) {
			$activated = 0;
			$type = $nxml->addChild('library');
			$type->addAttribute('handle', $sc->getSystemAntispamLibraryHandle());
			$type->addAttribute('name', $sc->getSystemAntispamLibraryName());
			$type->addAttribute('package', $sc->getPackageHandle());
			$type->addAttribute('activated', $sc->isSystemAntispamLibraryActive());
		}
	}
	
	
	public function hasOptionsForm() {
		$path = DIRNAME_SYSTEM . '/' . DIRNAME_SYSTEM_ANTISPAM . '/' . $this->saslHandle . '/' . FILENAME_FORM;
		if (file_exists(DIR_ELEMENTS . '/' . $path)) {
			return true;
		} else if ($this->pkgID > 0) {
			$pkgHandle = $this->getPackageHandle();
			$dp = DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_ELEMENTS . '/' . $path;
			$dpc = DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_ELEMENTS . '/' . $path;
			if (file_exists($dp)) {
				return true;
			} else if (file_exists($dpc)) {
				return true;
			}
		} else {
			return file_exists(DIR_ELEMENTS . '/' . $path);
		}
		
		return false;
	}
	
	/** 
	 * Returns the controller class for the currently selected captcha library
	 */
	public function getController() {
		$path = DIRNAME_SYSTEM . '/' . DIRNAME_SYSTEM_ANTISPAM . '/' . DIRNAME_SYSTEM_TYPES . '/' . $this->saslHandle . '/' . FILENAME_CONTROLLER;
		if (file_exists(DIR_MODELS . '/' . $path)) {
			require_once(DIR_MODELS . '/' . $path);
		} else if ($this->pkgID > 0) {
			$pkgHandle = $this->getPackageHandle();
			$dp = DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . $path;
			$dpc = DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . $path;
			if (file_exists($dp)) {
				require_once($dp);
			} else {
				require_once($dpc);
			}
		} else {
			require_once(DIR_MODELS_CORE . '/' . $path);
		}
		$txt = Loader::helper('text');
		$class = $txt->camelcase($this->saslHandle) . 'SystemAntispamTypeController';
		$cl = new $class();
		return $cl;
	}

}