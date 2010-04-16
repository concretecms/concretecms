<?php 
/**
 * @package Core
 * @category Concrete
 *
 */

/**
 * Pre PHP 5.2 compatibility class for the php DateTime class 
 * @package Core
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
if (!class_exists('DateTime')) {
	class DateTime {
		public $date;
	   
		public function __construct($date) {
			$this->date = strtotime($date);
		}
	   
		public function setTimeZone($timezone) {
			return;
		}
	   
		private function __getDate() {
			return date(DATE_ATOM, $this->date);   
		}
	   
		public function modify($multiplier) {
			$this->date = strtotime($this->__getDate() . ' ' . $multiplier);
		}
	   
		public function format($format) {
			return date($format, $this->date);
		}
	}
}

if(!class_exists('DateTimeZone')) {

	class DateTimeZone {
	
		public function __construct($timezone) {
			return NULL;
		}
	
		public function listIdentifiers() {
			return array(t('Default'));
		}
	}
}

?>
