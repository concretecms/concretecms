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

class ConfigValue extends Object {
	
	public $value;
	public $timestamp; // datetime value was set
	public $key;
}

class Config extends Object {
	private $pkg = false;
	private static $store;
	
	public static function setStore(ConfigStore $store)
	{
		self::$store = $store;
	}
	
	/**
	 * @return ConfigStore
	 */
	private static function getStore()
	{
		if (!self::$store) {
			self::$store = new ConfigStore(Loader::db());
		}
		return self::$store;
	}
	
	public function setPackageObject($pkg) {
		$this->pkg = $pkg;
	}
	
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
	
	public function getAndDefine($key, $defaultValue) {
		$val = Config::get($key);
		if ($val == null) {
			$val = $defaultValue;
		}
		define($key, $val);
	}
	
	public function clear($cfKey) {
		$pkgID = null;
		if (isset($this) && is_object($this->pkg)) {
			$pkgID = $this->pkg->getPackageID();
		}
		self::getStore()->delete($cfKey, $pkgID);
	}
	
	public function save($cfKey, $cfValue) {
		$pkgID = null;
		if (isset($this) && is_object($this->pkg)) {
			$pkgID = $this->pkg->getPackageID();
		}
		self::getStore()->set($cfKey, $cfValue, $pkgID);
	}
	
}

/**
 * Config Store that handles the saving and retrieval of ConfigValues
 * 
 * @package Utilities 
 * @author Christiaan Baartse <anotherhero@gmail.com>
 * @category Concrete
 */
class ConfigStore {
	/**
	 * @var Database
	 */
	private $db;
	
	/**
	 * @var array
	 */
	private $rows;
	
	public function __construct(Database $db) {
		$this->db = $db;
		$this->load();
	}
	
	private function load() {
		$val = Cache::get('config_options', 'all');
		if ($val) {
			$this->rows = $val;
		} else {
			$this->rows = array();
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
	}
	
	private function rowToConfigValue($row)
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
		if ($row['pkgID'] < 1) {
			$pkgID = 0;
		}
		$this->rows["{$cfKey}.{$pkgID}"] = array(
			'cfKey' => $cfKey,
			'timestamp' => $timestamp,
			'cfValue' => $cfValue,
			'uID' => 0,
			'pkgID' => $pkgID
		);
		$this->db->query(
			"replace into Config (cfKey, timestamp, cfValue, pkgID) values (?, ?, ?, ?)",
			array($cfKey, $timestamp, $cfValue, $pkgID)
		);
		Cache::set('config_options', 'all', $this->rows);
	}
	
	public function delete($cfKey, $pkgID = null) {
		if ($pkgID > 0) {
			unset($this->rows["{$cfKey}.{$pkgID}"]);
			$this->db->query(
				"delete from Config where cfKey = ? and pkgID = ?",
				array($cfKey, $pkgID)
			);
		} else {
			foreach ($this->rows as $key => $row) {
				if ($row['cfKey'] == $cfKey) {
					unset($this->rows[$key]);
				}
			}
			$this->db->query(
				"delete from Config where cfKey = ?",
				array($cfKey)
			);
		}
		Cache::set('config_options', 'all', $this->rows);
	}
}