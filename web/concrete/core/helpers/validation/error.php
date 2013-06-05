<?
/**
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Helper elements for dealing with errors in Concrete
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 	defined('C5_EXECUTE') or die("Access Denied.");
	class Concrete5_Helper_Validation_Error {
	
		protected $error = array();
		public $helperAlwaysCreateNewInstance = true;

		/** 
		 * Adds an error object or exception to the internal error array
		 * @param Exception | string $e
		 * @return void
		 */
		public function add($e) {
			if ($e instanceof ValidationErrorHelper) {
				foreach($e->getList() as $errorString) {
					$this->add($errorString);
				}
			} else if (is_object($e) && ($e instanceof Exception)) {
				$this->error[] = $e->getMessage();
			} else {
				$this->error[] = $e;
			}
		}
		
		/** 
		 * Returns a list of errors in the error helper
		 * @return array
		 */
		public function getList() {
			return $this->error;
		}
		
		/** 
		 * Returns whether or not this error helper has more than one error registered within it.
		 * @return bool
		 */
		public function has() {
			return (count($this->error) > 0);
		}

		/** 
		 * Outputs the HTML of an error list, with the correct style attributes/classes. This is a convenience method.
		 */
		public function output() {
			if ($this->has()) {
				print '<ul class="ccm-error">';
				foreach($this->getList() as $error) {
					print '<li>' . $error . '</li>';
				}
				print '</ul>';
			}
		}

		/** 
		 * Outputs the the error as a JSON object.
		 */
		public function outputJSON() {
			if ($this->has()) {
				$js = Loader::helper('json');
				$obj = new stdClass;
				$obj->error = true;
				$obj->messages = array();
				foreach($this->getList() as $error) {
					$obj->messages[] = $error;
				}
				print $js->encode($obj);
			}
		}

	}
	
?>