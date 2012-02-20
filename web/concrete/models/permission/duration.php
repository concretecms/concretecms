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
	public function setRepeats($pdRepeat) {$this->pdRepeat = $pdRepeat;}
	public function setRepeatPeriod($pdRepeatPeriod) {$this->pdRepeatPeriod = $pdRepeatPeriod;}
	public function setRepeatPeriodWeekDays($pdRepeatPeriodWeeksDays) {$this->pdRepeatPeriodWeeksDays = $pdRepeatPeriodWeeksDays;}
	public function setRepeatEveryNum($pdRepeatEveryNum) {$this->pdRepeatEveryNum = $pdRepeatEveryNum;}
	public function setRepeatPeriodEnd($pdRepeatPeriodEnd) {$this->pdRepeatPeriodEnd = $pdRepeatPeriodEnd;}
	
	
	
}

