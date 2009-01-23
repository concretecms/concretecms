<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

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
	
	public function get($cfKey, $getFullObject = false) {
		static $instance;
		if (!isset($instance)) {
			$v = __CLASS__;
			$instance = new $v;
		}

		$ca = new Cache();
		$cv = $ca->get('config_option', $cfKey);
		if (!($cv instanceof ConfigValue)) {
			$db = Loader::db();
			$val = @$db->GetRow("select timestamp, cfValue from Config where cfKey = ?", array($cfKey));
			if (!$val) {
				$val = $db->GetRow("select cfValue from Config where cfKey = ?", array($cfKey));
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

			$ca->set('config_option', $cfKey, $cv);		
		}

		if (!$getFullObject) {
			return $cv->value;
		} else {
			return $cv;
		}
	}
	
	public function getOrDefine($key, $defaultValue) {
		$val = Config::get($key);
		if ($val == null) {
			$val = $defaultValue;
		}
		define($key, $val);
	}
	
	public function save($cfKey, $cfValue) {
		$db = Loader::db();
		Cache::delete('config_option', $cfKey);
		$db->query("replace into Config (cfKey, cfValue) values (?, ?)", array($cfKey, $cfValue));
	}	
	
}