<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_JobSet extends Object {
	
	const DEFAULT_JOB_SET_ID = 1;
	
	public static function getList() {
		$db = Loader::db();
		$r = $db->Execute('select jsID from JobSets order by jsName asc');
		$list = array();
		while ($row = $r->FetchRow()) {
			$list[] = JobSet::getByID($row['jsID']);
		}
		return $list;
	}	

	public static function getByID($jsID) {
		$db = Loader::db();
		$row = $db->GetRow('select jsID, pkgID, jsName from JobSets where jsID = ?', array($jsID));
		if (isset($row['jsID'])) {
			$js = new JobSet();
			$js->setPropertiesFromArray($row);
			return $js;
		}
	}

	public static function getByName($jsName) {
		$db = Loader::db();
		$row = $db->GetRow('select jsID, pkgID, jsName from JobSets where jsName = ?', array($jsName));
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
	
	public function getJobSetID() {return $this->jsID;}
	public function getJobSetName() {return $this->jsName;}
	public function getPackageID() {return $this->pkgID;}
	
	public function updateJobSetName($jsName) {
		$this->jsName = $jsName;
		$db = Loader::db();
		$db->Execute("update JobSets set jsName = ? where jsID = ?", array($jsName, $this->jsID));
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
		
}