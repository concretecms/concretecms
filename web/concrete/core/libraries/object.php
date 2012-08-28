<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
*
* @package Utilities
* The object class is extended by most objects in Concrete, but is mostly internal
* @access private 
*
*/
	class Concrete5_Library_Object {
	
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

		public function uncamelcase($string) {
			$v = preg_split('/([A-Z])/', $string, false, PREG_SPLIT_DELIM_CAPTURE);
			$a = array();
			array_shift($v);
			for($i = 0; $i < count($v); $i++) {
				if ($i % 2) {
					if (function_exists('mb_strtolower')) {
						$a[] = mb_strtolower($v[$i - 1] . $v[$i], APP_CHARSET);
					} else {
						$a[] = strtolower($v[$i - 1] . $v[$i]);
					}
				}
			}
			return str_replace('__', '_', implode('_', $a));
		}		
	
	}