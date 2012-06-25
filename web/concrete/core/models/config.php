<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Contains the config class.
 * @package Utilities 
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * The config object holds global site-wide values for specific settings, allowing them to easily be changed
 * without having to visit a PHP configuration file.
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

class Concrete5_Model_ConfigValue extends Object {
	
	public $value;
	public $timestamp; // datetime value was set
	public $key;
}

class Concrete5_Model_Config extends Object {
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
		if (isset($this) && is_object($this->pkg)) {
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
		$val = Config::get($key, true);
		if (!$val) {
			$val = $defaultValue;
		} else {
			$val = $val->value;
		}
		define($key, $val);
	}
	/**
	* Clears a gived config key
	* @param string $cfKey
	*/
	public function clear($cfKey) {
		$pkgID = null;
		if (isset($this) && is_object($this->pkg)) {
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
			$option = $nconfig->addChild($row['cfKey'], $row['cfValue']);
			if ($row['pkgID'] > 0) {
				$pkg = Package::getByID($row['pkgID']);
				if (is_object($pkg)) {
					$option->addAttribute('package', $pkg->getPackageHandle());
				}
			}
		}
	}
	
}

/**
 * Config Store that handles the saving and retrieval of ConfigValues
 * 
 * @package Utilities 
 * @author Christiaan Baartse <anotherhero@gmail.com>
 * @category Concrete
 */
class Concrete5_Model_ConfigStore {
	/**
	 * @var Database
	 */
	protected $db;
	
	/**
	 * @var array
	 */
	protected $rows;
	
	public function __construct() {
		$this->load();
	}
	
	protected function load() {
		if (defined('ENABLE_CACHE') && (!ENABLE_CACHE)) {
			// if cache has been explicitly disabled, we re-enable it anyway.
			Cache::enableCache();
		}
		$val = Cache::get('config_options', 'all');
		if ($val) {
			$this->rows = $val;
		} else {
			$this->rows = array();
			$this->db = Loader::db();
			if (!$this->db) {
				return;
			}
			$r = $this->db->Execute('select * from Config where uID = 0 order by cfKey asc');
			while ($row = $r->FetchRow()) {
				if (!$row['pkgID']) {
					$row['pkgID'] = 0;
				}
				$this->rows["{$row['cfKey']}.{$row['pkgID']}"] = $row;
			}
			$r->Close();
			Cache::set('config_options', 'all', $this->rows);
		}
		if (defined('ENABLE_CACHE') && (!ENABLE_CACHE)) {
			Cache::disableCache();
		}
	}
	
	protected function rowToConfigValue($row)
	{
		$cv = new ConfigValue();
		$cv->key = $row['cfKey'];
		$cv->value = isset($row['cfValue']) ? $row['cfValue'] : '';
		$cv->timestamp = isset($row['timestamp']) ? $row['timestamp'] : '';
		return $cv;
	}
	
	/**
	 * Get a config item 
	 * @param string $cfKey
	 * @param int $pkgID optional
	 * @return ConfigValue|void
	 */
	public function get($cfKey, $pkgID = null) {
		if ($pkgID > 0 && isset($this->rows["{$cfKey}.{$pkgID}"])) {
			return $this->rowToConfigValue($this->rows["{$cfKey}.{$pkgID}"]);
		} else {
			foreach ($this->rows as $row) {
				if ($row['cfKey'] == $cfKey) {
					return $this->rowToConfigValue($row);
				}
			}
		}
		return null;
	}
	
	public function getListByPackage($pkgID) {
		$list = array();
		foreach ($this->rows as $row) {
			if ($row['pkgID'] == $pkgID) {
				$list[] = $row['cfKey'];
			}
		}
		return $list;
	}
	
	public function set($cfKey, $cfValue, $pkgID = 0) {
		$timestamp = date('Y-m-d H:i:s');
		if ($pkgID < 1) {
			$pkgID = 0;
		}
		$this->rows["{$cfKey}.{$pkgID}"] = array(
			'cfKey' => $cfKey,
			'timestamp' => $timestamp,
			'cfValue' => $cfValue,
			'uID' => 0,
			'pkgID' => $pkgID
		);
		$db = Loader::db();
		if (!$db) {
			return;
		}
		
		$db->query(
			"replace into Config (cfKey, timestamp, cfValue, pkgID) values (?, ?, ?, ?)",
			array($cfKey, $timestamp, $cfValue, $pkgID)
		);
		if (defined('ENABLE_CACHE') && (!ENABLE_CACHE)) {
			// if cache has been explicitly disabled, we re-enable it anyway.
			Cache::enableCache();
		}
		Cache::set('config_options', 'all', $this->rows);
		if (defined('ENABLE_CACHE') && (!ENABLE_CACHE)) {
			// if cache has been explicitly disabled, we re-enable it anyway.
			Cache::disableCache();
		}
	}
	
	public function delete($cfKey, $pkgID = null) {
		$db = Loader::db();
		if ($pkgID > 0) {
			unset($this->rows["{$cfKey}.{$pkgID}"]);
			$db->query(
				"delete from Config where cfKey = ? and pkgID = ?",
				array($cfKey, $pkgID)
			);
		} else {
			foreach ($this->rows as $key => $row) {
				if ($row['cfKey'] == $cfKey) {
					unset($this->rows[$key]);
				}
			}
			$db->query(
				"delete from Config where cfKey = ?",
				array($cfKey)
			);
		}
		if (defined('ENABLE_CACHE') && (!ENABLE_CACHE)) {
			// if cache has been explicitly disabled, we re-enable it anyway.
			Cache::enableCache();
		}
		Cache::set('config_options', 'all', $this->rows);
		if (defined('ENABLE_CACHE') && (!ENABLE_CACHE)) {
			// if cache has been explicitly disabled, we re-enable it anyway.
			Cache::disableCache();
		}
	}
	
}