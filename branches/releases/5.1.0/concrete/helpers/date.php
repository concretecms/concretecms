<?php 
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful functions for working with dates.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die(_("Access Denied."));
class DateHelper {

	/** 
	 * Gets the date time for the local time zone/area. 
	 * @todo: This currently doesn't hook into any kind of useful setting, and it probably should
	 * @param string $dateMask
	 * @return string $datetime
	 */
	function getLocalDateTime($dateMask = null) {
		/*$current_hour = substr(date('O'), 0, strlen(date('0')) - 3);
		$current_time = getdate();
		
		if ($dateMask) {
			return date($dateMask,mktime($current_time['hours'] + $current_hour, $current_time['minutes'], $curent_time['seconds'], $current_time['mon'], $current_time['mday'], $current_time['year']));
		} else {
			// we return standard mysql datetime
			return date('Y-m-d H:i:s',mktime($current_time['hours'] + $current_hour, $current_time['minutes'], $current_time['seconds'], $current_time['mon'], $current_time['mday'], $current_time['year']));
		}*/
		
		return date('Y-m-d H:i:s');
	}


}

?>