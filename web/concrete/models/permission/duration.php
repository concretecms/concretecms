<?
defined('C5_EXECUTE') or die("Access Denied.");
class PermissionDuration extends Object {

	public function getPermissionDurationID() { return $this->pdID;}
	
	public static function getByID($pdID) {
		$db = Loader::db();
		$pdObject = $db->getOne('select pdObject from PermissionDurationObjects where pdID = ?', array($pdID));
		if ($pdObject) {
			$pd = unserialize($pdObject);
			return $pd;
		}
	}
	
	public function save() {
		$db = Loader::db();
		if (!$this->pdID) {
			$pd = new PermissionDuration();
			$pdObject = serialize($pd);
			$db->Execute('insert into PermissionDurationObjects (pdObject) values (?)', array($pdObject));
			$this->pdID = $db->Insert_ID();
		}
		$pdObject = serialize($this);
		$db->Execute('update PermissionDurationObjects set pdObject = ? where pdID = ?', array($pdObject, $this->pdID));
	}
	
	public function setStartDate($pdStartDate) {$this->pdStartDate = $pdStartDate;}
	public function setEndDate($pdEndDate) {$this->pdEndDate = $pdEndDate;}
	public function setRepeatPeriod($pdRepeatPeriod) {$this->pdRepeatPeriod = $pdRepeatPeriod;}
	public function setRepeatPeriodWeekDays($pdRepeatPeriodWeeksDays) {$this->pdRepeatPeriodWeeksDays = $pdRepeatPeriodWeeksDays;}
	public function setRepeatEveryNum($pdRepeatEveryNum) {$this->pdRepeatEveryNum = $pdRepeatEveryNum;}
	public function setRepeatMonthBy($pdRepeatPeriodMonthsRepeatBy) {$this->pdRepeatPeriodMonthsRepeatBy = $pdRepeatPeriodMonthsRepeatBy;}
	public function setRepeatPeriodEnd($pdRepeatPeriodEnd) {$this->pdRepeatPeriodEnd = $pdRepeatPeriodEnd;}
	
	public function getStartDate() {return $this->pdStartDate;}
	public function getEndDate() {return $this->pdEndDate;}
	public function repeats() {
		return (in_array($this->pdRepeatPeriod, array('daily','weekly','monthly')));
	}
	public function getRepeatPeriod() {return $this->pdRepeatPeriod;}
	public function getRepeatPeriodWeekDays() {
		if (is_array($this->pdRepeatPeriodWeeksDays)) {
			return $this->pdRepeatPeriodWeeksDays;
		} else {
			return array();
		}
	}
	public function getRepeatMonthBy() {return $this->pdRepeatPeriodMonthsRepeatBy;}
	public function getRepeatPeriodEveryNum() {return $this->pdRepeatEveryNum;}
	public function getRepeatPeriodEnd() {return $this->pdRepeatPeriodEnd;}	
	
	public function isActive() {
		$now = strtotime(Loader::helper('date')->getLocalDateTime());
		if (!$this->repeats()) { 
			$isActive = true;
			if ($this->getStartDate() != '' && strtotime($this->getStartDate()) > $now) {
				$isActive = false;
			}
			if ($this->getEndDate() != '' && strtotime($this->getEndDate()) < $now) {
				$isActive = false;
			}
		} else {
			$isActive = false;
			$startsOn = date('Y-m-d', strtotime($this->getStartDate()));
			$ymd = date('Y-m-d', $now);
			$dailyTimeStart = strtotime($ymd . ' ' . date('H:i:s', strtotime($this->getStartDate())));
			$dailyTimeEnd = strtotime($ymd . ' ' . date('H:i:s', strtotime($this->getEndDate())));
			switch($this->getRepeatPeriod()) {
				case 'daily':
					// number of days between now and the start
					$numDays = round(($now - strtotime($startsOn)) / 86400);
					if (($numDays % $this->getRepeatPeriodEveryNum()) == 0) {
						if ($now >= $dailyTimeStart && $now <= $dailyTimeEnd) {
							$isActive = true;
						}
					}
					break;
				case 'weekly':
					$numWeeks = round(($now - strtotime($startsOn)) / (86400 * 7));
					if (($numWeeks % $this->getRepeatPeriodEveryNum()) == 0) {
						// now we check to see if it's on the right day
						$days = $this->getRepeatPeriodWeekDays();
						$dow = date('w', $now);
						if (in_array($dow, $days)) { 
							if ($now >= $dailyTimeStart && $now <= $dailyTimeEnd) {
								$isActive = true;
							}
						}
					}
					break;
				case 'monthly':
					$numMonths = round(($now - strtotime($startsOn)) / (86400 * 30));
					$checkTime = false;
					if (($numMonths % $this->getRepeatPeriodEveryNum()) == 0) {
						// now we check to see if it's on the right day
						if ($this->getRepeatMonthBy() == 'month') {
							// that means it has to be the same day of the month. e.g. the 29th, etc..
							if (date('d', $now) == date('d', strtotime($this->getStartDate()))) {
								$checkTime = true;
							}
						} else if ($this->getRepeatMonthBy() == 'week') {
							// the last sunday? etc..
							$savedWeekNum = date("W", strtotime($this->getStartDate())) - date("W", strtotime(date("Y-m-01", strtotime($this->getStartDate())))) + 1;
							$nowWeekNum = date("W", $now) - date("W", strtotime(date("Y-m-01", $now))) + 1;
							if ($savedWeekNum == $nowWeekNum) {
								if (date('d', $now) == date('d', strtotime($this->getStartDate()))) {
									$checkTime = true;
								}
							}								
						}
						
						if ($checkTime) {
							if ($now >= $dailyTimeStart && $now <= $dailyTimeEnd) {
								$isActive = true;
							}
						}
					}
					break;

			}
		}		
		return $isActive;
	}

	public static function filterByActive($list) {
		$filteredList = array();
		foreach($list as $l) { 
			$pd = $l->getPermissionDurationObject();
			if (is_object($pd)) { 
				if ($pd->isActive()) { 					
					$filteredList[] = $l;
				}
			} else { 
				$filteredList[] = $l;
			}
		}
		return $filteredList;
	}
	
}

