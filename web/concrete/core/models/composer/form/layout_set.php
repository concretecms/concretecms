<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerFormLayoutSet extends Object {

	public function getComposerFormLayoutSetID() {return $this->cmpFormLayoutSetID;}
	public function getComposerFormLayoutSetName() {return $this->cmpFormLayoutSetName;}
	public function getComposerFormLayoutSetDisplayOrder() {return $this->cmpFormLayoutSetDisplayOrder;}
	public function getComposerID() {return $this->cmpID;}
	public function getComposerObject() {return Composer::getByID($this->cmpID);}

	public static function getList(Composer $composer) {
		$db = Loader::db();
		$cmpFormLayoutSetIDs = $db->GetCol('select cmpFormLayoutSetID from ComposerFormLayoutSets order by cmpFormLayoutSetDisplayOrder asc');
		$list = array();
		foreach($cmpFormLayoutSetIDs as $cmpFormLayoutSetID) {
			$set = ComposerFormLayoutSet::getByID($cmpFormLayoutSetID);
			if (is_object($set)) {
				$list[] = $set;
			}
		}
		return $list;
	}

	public static function getByID($cmpFormLayoutSetID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from ComposerFormLayoutSets where cmpFormLayoutSetID = ?', array($cmpFormLayoutSetID));
		if (is_array($r) && $r['cmpFormLayoutSetID']) {
			$set = new ComposerFormLayoutSet;
			$set->setPropertiesFromArray($r);
			return $set;
		}
	}

	public function updateFormLayoutSetDisplayOrder($displayOrder) {
		$db = Loader::db();
		$db->Execute('update ComposerFormLayoutSets set cmpFormLayoutSetDisplayOrder = ? where cmpFormLayoutSetID = ?', array(
			$displayOrder, $this->cmpFormLayoutSetID
		));
		$this->cmpFormLayoutSetDisplayOrder = $displayOrder;
	}

	public function delete() {
		/*
		$db = Loader::db();
		$db->Execute('delete from Composers where cmpID = ?', array($this->cmpID));
		$db->Execute('delete from ComposerPageTypes where cmpID = ?', array($this->cmpID));
		*/
	}

}