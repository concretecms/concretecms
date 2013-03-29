<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_Composer extends Object {

	public function getComposerID() {return $this->cmpID;}
	public function getComposerName() {return $this->cmpName;}
	public function getComposerTargetTypeID() {return $this->cmpTargetTypeID;}
	public function getComposerTargetObject() {return $this->cmpTargetObject;}

	public function getComposerPageTypeObjects() {
		$db = Loader::db();
		$types = array();
		$r = $db->Execute('select ctID from ComposerPageTypes where cmpID = ? order by ctID asc', array($this->cmpID));
		while ($row = $r->FetchRow()) {
			$ct = CollectionType::getByID($row['ctID']);
			if (is_object($ct)) {
				$types[] = $ct;
			}
		}
		return $types;
	}

	public static function add($cmpName, $types) {
		$db = Loader::db();
		$db->Execute('insert into Composers (cmpName) values (?)', array(
			$cmpName
		));
		$cmpID = $db->Insert_ID();
		foreach($types as $ct) {
			$db->Execute('insert into ComposerPageTypes (cmpID, ctID) values (?, ?)', array(
				$cmpID, $ct->getCollectionTypeID()
			));
		}
		return Composer::getByID($db->Insert_ID());
	}

	/** 
	 * Returns an array of all areas on the page type defaults for the page types selected
	 */
	public function getPageTypeAreaList() {
		$db = Loader::db();
		$ctIDs = array(-1);
		foreach($this->getComposerPageTypeObjects() as $ct) {
			$ctIDs[] = $ct->getCollectionTypeID();
		}
		$r = $db->Execute('select distinct arHandle from Areas where cID in (select p.cID from Pages p inner join CollectionVersions cv on (p.cID = cv.cID and cv.cvIsApproved = 1) where cIsTemplate = 1 and ctID in (' . implode(',', $ctIDs) . ')) order by arHandle');
		$areas = array();
		while ($row = $r->FetchRow()) {
			$areas[] = $row['arHandle'];
		}
		return $areas;
	}

	public function update($cmpName, $types) {
		$db = Loader::db();
		$db->Execute('update Composers set cmpName = ? where cmpID = ?', array(
			$cmpName,
			$this->cmpID
		));
		$db->Execute('delete from ComposerPageTypes where cmpID = ?', array($this->cmpID));
		foreach($types as $ct) {
			$db->Execute('insert into ComposerPageTypes (cmpID, ctID) values (?, ?)', array(
				$this->cmpID, $ct->getCollectionTypeID()
			));
		}
	}

	public static function getList() {
		$db = Loader::db();
		$cmpIDs = $db->GetCol('select cmpID from Composers order by cmpName asc');
		$list = array();
		foreach($cmpIDs as $cmpID) {
			$cm = Composer::getByID($cmpID);
			if (is_object($cm)) {
				$list[] = $cm;
			}
		}
		return $list;
	}

	public static function getByID($cmpID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from Composers where cmpID = ?', array($cmpID));
		if (is_array($r) && $r['cmpID']) {
			$cm = new Composer;
			$cm->setPropertiesFromArray($r);
			$cm->cmpTargetObject = unserialize($r['cmpTargetObject']);
			return $cm;
		}
	}

	public function delete() {
		$sets = ComposerFormLayoutSet::getList($this);
		foreach($sets as $set) {
			$set->delete();
		}
		$db = Loader::db();
		$db->Execute('delete from Composers where cmpID = ?', array($this->cmpID));
		$db->Execute('delete from ComposerPageTypes where cmpID = ?', array($this->cmpID));
		$db->Execute('delete from ComposerOutputControls where cmpID = ?', array($this->cmpID));
	}

	public function setConfiguredComposerTargetObject(ComposerTargetConfiguration $configuredTarget) {
		$db = Loader::db();
		if (is_object($configuredTarget)) {
			$db->Execute('update Composers set cmpTargetTypeID = ?, cmpTargetObject = ? where cmpID = ?', array(
				$configuredTarget->getComposerTargetTypeID(),
				@serialize($configuredTarget),
				$this->getComposerID()
			));
		}
	}

	public function rescanFormLayoutSetDisplayOrder() {
		$sets = ComposerFormLayoutSet::getList($this);
		$displayOrder = 0;
		foreach($sets as $s) {
			$s->updateFormLayoutSetDisplayOrder($displayOrder);
			$displayOrder++;
		}
	}

	public function addComposerFormLayoutSet($cmpFormLayoutSetName) {
		$db = Loader::db();
		$displayOrder = $db->GetOne('select count(cmpFormLayoutSetID) from ComposerFormLayoutSets where cmpID = ?', array($this->cmpID));
		if (!$displayOrder) {
			$displayOrder = 0;
		}
		$db->Execute('insert into ComposerFormLayoutSets (cmpFormLayoutSetName, cmpID, cmpFormLayoutSetDisplayOrder) values (?, ?, ?)', array(
			$cmpFormLayoutSetName, $this->cmpID, $displayOrder
		));	
		return ComposerFormLayoutSet::getByID($db->Insert_ID());
	}

}