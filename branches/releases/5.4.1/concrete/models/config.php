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
	
	private $props = array();
	private $pkg = false;
	
	public function setPackageObject($pkg) {
		$this->pkg = $pkg;
	}
	public function get($cfKey, $getFullObject = false) {
		static $instance;
		if (!isset($instance)) {
			$v = __CLASS__;
			$instance = new $v;
		}

		$ca = new Cache();
		$pkgID = '';
		if (isset($this) && is_object($this->pkg)) {
			$pkgID = $this->pkg->getPackageID();
		}
		
		$cv = $ca->get('config_option' . $pkgID, $cfKey);
		
		if ((!isset($cv)) || (!($cv instanceof ConfigValue))) {
			$db = Loader::db();
			$v = array($cfKey);
			$qs = '';
			if ($pkgID > 0) {
				$v[] = $pkgID;
				$qs = ' and pkgID = ?';
			}
			
			$val = @$db->GetRow("select timestamp, cfValue from Config where cfKey = ?" . $qs, $v);
			if (!$val) {
				$val = $db->GetRow("select cfValue from Config where cfKey = ?" . $qs, $v);
			}
			
			$cfValue = '';
			$timestamp = '';
			if (isset($val['cfValue'])) {
				$cfValue = $val['cfValue'];
			}
			if (isset($val['timestamp'])) {
				$timestamp = $val['timestamp'];
			}
			
			$cv = new ConfigValue();
			$cv->value = $cfValue;
			$cv->key = $cfKey;
			$cv->timestamp = $timestamp;

			$ca->set('config_option' . $pkgID, $cfKey, $cv);		
		}

		if (!$getFullObject) {
			$value = $cv->value;
			unset($cv);
			return $value;
		} else {
			return $cv;
		}
	}
	
	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select cfKey from Config where pkgID = ? order by cfKey asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = $pkg->config($row['cfKey'], true);
		}
		$r->Close();
		return $list;
	}	
	
	public function getOrDefine($key, $defaultValue) {
		$val = Config::get($key);
		if ($val == null) {
			$val = $defaultValue;
		}
		define($key, $val);
	}
	
	public function clear($cfKey) {
		$db = Loader::db();
		$pkgID = '';
		if (isset($this) && is_object($this->pkg)) {
			$pkgID = $this->pkg->getPackageID();
			$db->query("delete from Config where cfKey = ? and pkgID = ?", array($cfKey, $pkgID));
		} else {
			$db->query("delete from Config where cfKey = ?", array($cfKey));
		}
		Cache::delete('config_option' . $pkgID, $cfKey);
	}
	
	public function save($cfKey, $cfValue) {
		$db = Loader::db();
		$pkgID = '';
		if (isset($this) && is_object($this->pkg)) {
			$pkgID = $this->pkg->getPackageID();
			$db->query("replace into Config (cfKey, cfValue, pkgID) values (?, ?, ?)", array($cfKey, $cfValue, $pkgID));
		} else {
			$db->query("replace into Config (cfKey, cfValue) values (?, ?)", array($cfKey, $cfValue));
		}
		Cache::delete('config_option' . $pkgID, $cfKey);
	}
	
}