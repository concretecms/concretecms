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


	function timeSince($posttime,$precise=0){
		$timeRemaining=0;
		$diff=date("U")-$posttime;
		$days=intval($diff/(24*60*60));
		$hoursInSecs=$diff-($days*(24*60*60));
		$hours=intval($hoursInSecs/(60*60));
		if ($hours<=0) $hours=$hours+24;           
		if ($posttime>date("U")) return date("n/j/y",$posttime);
		else{
			if ($diff>86400){
					$diff=$diff+86400;
					$days=date("z",$diff);
					$timeRemaining=$days.' '.t('day');
					if($days!=1) $timeRemaining.=t('s');
					if($precise==1) $timeRemaining.=', '.$hours.' '.t('hours');
				} else if ($diff>3600) {
					$timeRemaining=$hours.' '.t('hour');
					if($hours!=1) $timeRemaining.=t('s');
					if($precise==1) $timeRemaining.=', '.date("i",$diff).' '.t('minutes');
				}else if ($diff>60){
					$minutes=date("i",$diff);
					if(substr($minutes,0,1)=='0') $minutes=substr($minutes,1);
					$timeRemaining=$minutes.' '.t('minute');
					if($minutes!=1) $timeRemaining.=t('s');
					if($precise==1) $timeRemaining.=', '.date("s",$diff).' '.t('seconds');
				}else{
					$seconds=date("s",$diff);
					if(substr($seconds,0,1)=='0') $seconds=substr($seconds,1);
					$timeRemaining=$seconds.' '.t('second');
					if($seconds!=1) $timeRemaining.=t('s');
				}
		}
		return $timeRemaining;
	}//end timeSince

}

?>