<?php
namespace Concrete\Core\Captcha;
use \Concrete\Core\Foundation\Object;
use Loader;
use Core;
use \Concrete\Core\Package\PackageList;
class Library extends Object {

	public function getSystemCaptchaLibraryHandle() { return $this->sclHandle;}
	public function getSystemCaptchaLibraryName() { return $this->sclName;}
	public function isSystemCaptchaLibraryActive() { return $this->sclIsActive;}
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	public function getPackageObject() {return Package::getByID($this->pkgID);}

	public static function getActive() {
		$db = Loader::db();
		$sclHandle = $db->GetOne('select sclHandle from SystemCaptchaLibraries where sclIsActive = 1');
		return static::getByHandle($sclHandle);
	}

	public static function getByHandle($sclHandle) {
		$db = Loader::db();
		$r = $db->GetRow('select sclHandle, sclIsActive, pkgID, sclName from SystemCaptchaLibraries where sclHandle = ?', array($sclHandle));
		if (is_array($r) && $r['sclHandle']) {
			$sc = new static();
			$sc->setPropertiesFromArray($r);
			return $sc;
		}
	}

	public static function add($sclHandle, $sclName, $pkg = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('insert into SystemCaptchaLibraries (sclHandle, sclName, pkgID) values (?, ?, ?)', array($sclHandle, $sclName, $pkgID));
		return static::getByHandle($sclHandle);
	}

	public function delete() {
		$db = Loader::db();
		if(static::getActive()->getSystemCaptchaLibraryHandle() == $this->sclHandle) {
			if ($scl = static::getByHandle('securimage')) {
				$scl->activate();
			}
		}
		$db->Execute('delete from SystemCaptchaLibraries where sclHandle = ?', array($this->sclHandle));
	}

	public function activate() {
		$db = Loader::db();
		$db->Execute('update SystemCaptchaLibraries set sclIsActive = 0');
		$db->Execute('update SystemCaptchaLibraries set sclIsActive = 1 where sclHandle = ?', array($this->sclHandle));
	}

	public static function getList() {
		$db = Loader::db();
		$sclHandles = $db->GetCol('select sclHandle from SystemCaptchaLibraries order by sclHandle asc');
		$libraries = array();
		foreach($sclHandles as $sclHandle) {
			$scl = static::getByHandle($sclHandle);
			$libraries[] = $scl;
		}
		return $libraries;
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$sclHandles = $db->GetCol('select sclHandle from SystemCaptchaLibraries where pkgID = ? order by sclHandle asc', array($pkg->getPackageID()));
		$libraries = array();
		foreach($sclHandles as $sclHandle) {
			$scl = static::getByHandle($sclHandle);
			$libraries[] = $scl;
		}
		return $libraries;
	}

	public static function exportList($xml) {
		$list = self::getList();
		$nxml = $xml->addChild('systemcaptcha');

		foreach($list as $sc) {
			$activated = 0;
			$type = $nxml->addChild('library');
			$type->addAttribute('handle', $sc->getSystemCaptchaLibraryHandle());
			$type->addAttribute('name', $sc->getSystemCaptchaLibraryName());
			$type->addAttribute('package', $sc->getPackageHandle());
			$type->addAttribute('activated', $sc->isSystemCaptchaLibraryActive());
		}
	}


	public function hasOptionsForm() {
		$path = DIRNAME_SYSTEM . '/' . DIRNAME_SYSTEM_CAPTCHA . '/' . $this->sclHandle . '/' . FILENAME_FORM;
		if (file_exists(DIR_FILES_ELEMENTS . '/' . $path)) {
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
			return file_exists(DIR_FILES_ELEMENTS_CORE . '/' . $path);
 		}

		return false;
	}

	/**
	 * Returns the controller class for the currently selected captcha library
	 */
	public function getController() {
        $class = overrideable_core_class('Core\\Captcha\\'
            . Loader::helper('text')->camelcase($this->sclHandle) . 'Controller', DIRNAME_CLASSES . '/Captcha/'
            . Loader::helper('text')->camelcase($this->sclHandle) . 'Controller.php',
            $this->getPackageHandle()
        );
		$cl = Core::make($class);
		return $cl;
	}

}
