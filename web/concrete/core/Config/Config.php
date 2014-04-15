<?php
namespace Concrete\Core\Config;
use \Concrete\Core\Foundation\Object;
use Loader;
class Config extends Object {
	protected $pkg = false;
	protected static $store;
	
	public static function setStore(ConfigStore $store)
	{
		self::$store = $store;
	}
	
	/**
	 * @return ConfigStore
	 */
	protected static function getStore()
	{
		if (!self::$store) {
			self::$store = new ConfigStore();
		}
		return self::$store;
	}
	
	public function setPackageObject($pkg) {
		$this->pkg = $pkg;
	}
	/**
	* Gets the config value for a given key
	* @param string $cfKey
	* @param bool $getFullObject
	* @return string or full object $cv
	*/
	public function get($cfKey, $getFullObject = false) {
		$pkgID = null;
		/*
		 * We need to check that it is an instance of Concrete5_Model_Config
		 * becuase Config::get() is called statically and if the object calling
		 * Config::get() has a property $pkg that is an object we'll actually
		 * get that instead of what we want
		 */
		if (isset($this) && $this instanceof Concrete5_Model_Config && is_object($this->pkg)) {
			$pkgID = $this->pkg->getPackageID();
		}
		
		$cv = self::getStore()->get($cfKey, $pkgID);

		if (!$getFullObject) {
			if (is_object($cv)) {
				$value = $cv->value;
				unset($cv);
				return $value;
			}
		} else {
			return $cv;
		}
	}
	/**
	* gets a list of all the configs associated with a package
	* @param package object $pkg
	* @return array $list
	*/
	public static function getListByPackage($pkg) {
		$res = self::getStore()->getListByPackage($pkg->getPackageID());
		$list = array();
		foreach ($res as $key) {
			$list[] = $pkg->config($key, true);
		}
		return $list;
	}	
	
	// Misleading old functionname
	public function getOrDefine($key, $defaultValue) {
		return self::getAndDefine($key, $defaultValue);
	}
	/**
	* Checks to see if the given key is defined or not
	* if it isn't then it is defined as the default value
	* @param string $key
	* @param string $defaultValue
	*/
	public function getAndDefine($key, $defaultValue) {
		if (!defined($key)) {
			$val = Config::get($key, true);
			if (!$val) {
				$val = $defaultValue;
			} else {
				$val = $val->value;
			}
			define($key, $val);
		}
	}
	/**
	* Clears a gived config key
	* @param string $cfKey
	*/
	public function clear($cfKey) {
		$pkgID = null;
		/*
		 * See Config::get() for info on why instanceof is needed etc
		 */
		if (isset($this) && $this instanceof Concrete5_Model_Config && is_object($this->pkg)) {
			$pkgID = $this->pkg->getPackageID();
		}
		self::getStore()->delete($cfKey, $pkgID);
	}
	/**
	* Saves a given value to a key
	* @param string $cfkey
	* @param string $cfValue
	*/
	public function save($cfKey, $cfValue) {
		$pkgID = null;
		if (isset($this) && is_object($this->pkg)) {
			$pkgID = $this->pkg->getPackageID();
		}
		self::getStore()->set($cfKey, $cfValue, $pkgID);
	}

	public static function exportList($x) {
		$nconfig = $x->addChild('config');
		$db = Loader::db();
		$r = $db->Execute("select cfKey, cfValue, pkgID from Config where uID = 0 and cfKey not in ('SITE','SITE_APP_VERSION','SEEN_INTRODUCTION')");
		while ($row = $r->FetchRow()) {
			$option = $nconfig->addChild($row['cfKey']);
			$node = dom_import_simplexml($option);
			$no = $node->ownerDocument;
			$node->appendChild($no->createCDataSection($row['cfValue']));
			if ($row['pkgID'] > 0) {
				$pkg = Package::getByID($row['pkgID']);
				if (is_object($pkg)) {
					$option->addAttribute('package', $pkg->getPackageHandle());
				}
			}
		}
	}
	
}