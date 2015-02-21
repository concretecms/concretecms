<?php
namespace Concrete\Core\Job;
use Gettext\Translations;
use Loader;
use JobSet;
use Environment;
use \Concrete\Core\Foundation\Object;
class Set extends Object {

	const DEFAULT_JOB_SET_ID = 1;

	public $jDateLastRun;
	public $isScheduled = 0;
	public $scheduledInterval = 'days'; // hours|days|weeks|months
	public $scheduledValue = 0;

	/**
	 * @return JobSet[]
	 */
	public static function getList() {
		$db = Loader::db();
		$r = $db->Execute('select jsID, pkgID, jsName, jDateLastRun, isScheduled, scheduledInterval, scheduledValue from JobSets order by jsName asc');
		$list = array();
		while ($row = $r->FetchRow()) {
			$js = new JobSet();
			$js->setPropertiesFromArray($row);
			$list[] = $js;
		}
		return $list;
	}

	public static function getByID($jsID) {
		$db = Loader::db();
		$row = $db->GetRow('SELECT jsID, pkgID, jsName, jDateLastRun, isScheduled, scheduledInterval, scheduledValue FROM JobSets WHERE jsID = ?', array($jsID));
		if (isset($row['jsID'])) {
			$js = new JobSet();
			$js->setPropertiesFromArray($row);
			return $js;
		}
	}

	public static function getByName($jsName) {
		$db = Loader::db();
		$row = $db->GetRow('SELECT jsID, pkgID, jsName, jDateLastRun, isScheduled, scheduledInterval, scheduledValue FROM JobSets WHERE jsName = ?', array($jsName));
		if (isset($row['jsID'])) {
			$js = new JobSet();
			$js->setPropertiesFromArray($row);
			return $js;
		}
	}


	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select jsID from JobSets where pkgID = ? order by jsID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = JobSets::getByID($row['jsID']);
		}
		$r->Close();
		return $list;
	}

	public static function getDefault() {
		$js = JobSet::getByID(self::DEFAULT_JOB_SET_ID);
		if (is_object($js)) {
			return $js;
		}
	}

	public function getJobSetID() {return $this->jsID;}
	public function getJobSetName() {return $this->jsName;}
	public function getPackageID() {return $this->pkgID;}

	/** Returns the display name for this job set (localized and escaped accordingly to $format)
	* @param string $format = 'html'
	*	Escape the result in html format (if $format is 'html').
	*	If $format is 'text' or any other value, the display name won't be escaped.
	* @return string
	*/
	public function getJobSetDisplayName($format = 'html') {
		$value = tc('JobSetName', $this->getJobSetName());
		switch($format) {
			case 'html':
				return h($value);
			case 'text':
			default:
				return $value;
		}
	}

	public function updateJobSetName($jsName) {
		$this->jsName = Loader::helper('security')->sanitizeString($jsName);
		$db = Loader::db();
		$db->Execute("update JobSets set jsName = ? where jsID = ?", array($this->jsName, $this->jsID));
	}

	public function addJob(Job $j) {
		$db = Loader::db();
		$no = $db->GetOne("select count(jID) from JobSetJobs where jID = ? and jsID = ?", array($j->getJobID(), $this->getJobSetID()));
		if ($no < 1) {
			$db->Execute('insert into JobSetJobs (jsID, jID) values (?, ?)', array($this->getJobSetID(), $j->getJobID()));
		}
	}

	public static function add($jsName, $pkg = false) {
		$db = Loader::db();
		$jsName = Loader::helper('security')->sanitizeString($jsName);
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db->Execute('insert into JobSets (jsName, pkgID) values (?,?)', array($jsName, $pkgID));
		$id = $db->Insert_ID();
		$js = JobSet::getByID($id);
		return $js;
	}

	public function clearJobs() {
		$db = Loader::db();
		$db->Execute('delete from JobSetJobs where jsID = ?', array($this->jsID));
	}

	/**
	 * @return Job[]
	 */
	public function getJobs() {
		$db = Loader::db();
		$r = $db->Execute('select jID from JobSetJobs where jsID = ? order by jID asc', $this->getJobSetId());
		$jobs = array();
		while ($row = $r->FetchRow()) {
			$j = Job::getByID($row['jID']);
			if (is_object($j)) {
				$jobs[] = $j;
			}
		}
		return $jobs;
	}

	public function markStarted(){
		$db = Loader::db();
		$timestamp=date('Y-m-d H:i:s');
		$this->jDateLastRun = $timestamp;
		$rs = $db->query( "UPDATE JobSets SET jDateLastRun=? WHERE jsID=?", array( $timestamp, $this->getJobSetID() ) );
	}


	public function contains(Job $j) {
		$db = Loader::db();
		$r = $db->GetOne('select count(jID) from JobSetJobs where jsID = ? and jID = ?', array($this->getJobSetID(), $j->getJobID()));
		return $r > 0;
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from JobSets where jsID = ?', array($this->getJobSetID()));
		$db->Execute('delete from JobSetJobs where jsID = ?', array($this->getJobSetID()));
	}

	public function canDelete() {
		return $this->jsID != self::DEFAULT_JOB_SET_ID;
	}

	public function removeJob(Job $j) {
		$db = Loader::db();
		$db->Execute('delete from JobSetJobs where jsID = ? and jID = ?', array($this->getJobSetID(), $j->getJobID()));
	}

	public function isScheduledForNow() {
		if(!$this->isScheduled) {
			return false;
		}

		if($this->scheduledValue <= 0) {
			return false;
		}

		$last_run = strtotime($this->jDateLastRun);
		$seconds = 1;
		switch($this->scheduledInterval) {
			case "hours":
				$seconds = 60*60;
				break;
			case "days":
				$seconds = 60*60*24;
				break;
			case "weeks":
				$seconds = 60*60*24*7;
				break;
			case "months":
				$seconds = 60*60*24*7*30;
				break;
		}
		$gap = $this->scheduledValue * $seconds;
		if($last_run < (time() - $gap) ) {
			return true;
		} else {
			return false;
		}
	}

	public function setSchedule($scheduled, $interval, $value) {
		$this->isScheduled = ($scheduled?true:false);
		$this->scheduledInterval = $interval;
		$this->scheduledValue = $value;
		if($this->getJobSetID()) {
			$db = Loader::db();
			$db->query("UPDATE JobSets SET isScheduled = ?, scheduledInterval = ?, scheduledValue = ? WHERE jsID = ?",
			array($this->isScheduled, $this->scheduledInterval, $this->scheduledValue, $this->getJobSetID()));
			return true;
		} else {
			return false;
		}
	}

    public static function exportTranslations()
    {
        $translations = new Translations();
        $attribs = static::getList();
        foreach($attribs as $set) {
            $translations->insert('JobSetName', $set->getJobSetName());
        }
        return $translations;
    }

}
