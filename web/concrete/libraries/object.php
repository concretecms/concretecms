<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * The object class is extended by most objects in concrete5. It adds some basic error storage and convenience functions for parameter population.
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */ 
	class Object {
	
		public $error = '';
		
		/* TODO: move these into an error class */
		
		function loadError($error) {
			$this->error = $error;
		}
		
		function isError() {
			$args = func_get_args();
			if ($args[0]) {
				return $this->error == $args[0];
			} else {
				return $this->error;
			}
		}
		
		function getError() {
			return $this->error;
		}
		
		public function setPropertiesFromArray($arr) {
			foreach($arr as $key => $prop) {
				$this->{$key} = $prop;
			}
		}
		
		public function camelcase($file) {
			// turns "asset_library" into "AssetLibrary"
			$r1 = ucwords(str_replace(array('_', '-', '/'), ' ', $file));
			$r2 = str_replace(' ', '', $r1);
			return $r2;		
		}

	
	}

?>