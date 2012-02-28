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
	
	public static function filterByActive($list) {
		$filteredList = array();
		foreach($list as $l) { 
			$pd = $l->getPermissionDurationObject();
			if (is_object($pd)) { 
				$now = strtotime(Loader::helper('date')->getLocalDateTime());
				if ($pd->getStartDate() != '' && strtotime($pd->getStartDate()) > $now) {
					continue;
				}
				if ($pd->getEndDate() != '' && strtotime($pd->getEndDate()) < $now) {
					continue;
				}
				
				$filteredList[] = $l;
			} else { 
				$filteredList[] = $l;
			}
		}
		return $filteredList;
	}
	
}

