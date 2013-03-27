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

	public function updateFormLayoutSetName($cmpFormLayoutSetName) {
		$db = Loader::db();
		$db->Execute('update ComposerFormLayoutSets set cmpFormLayoutSetName = ? where cmpFormLayoutSetID = ?', array(
			$cmpFormLayoutSetName, $this->cmpFormLayoutSetID
		));
		$this->cmpFormLayoutSetName = $cmpFormLayoutSetName;
	}


	public function updateFormLayoutSetDisplayOrder($displayOrder) {
		$db = Loader::db();
		$db->Execute('update ComposerFormLayoutSets set cmpFormLayoutSetDisplayOrder = ? where cmpFormLayoutSetID = ?', array(
			$displayOrder, $this->cmpFormLayoutSetID
		));
		$this->cmpFormLayoutSetDisplayOrder = $displayOrder;
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from ComposerFormLayoutSets where cmpFormLayoutSetID = ?', array($this->cmpFormLayoutSetID));
		$composer = $this->getComposerObject();
		$composer->rescanControlSetDisplayOrder();
	}

	public function addComposerControl(ComposerControl $control) {
		$db = Loader::db();
		$displayOrder = $db->GetOne('select count(cmpFormLayoutControlID) from ComposerFormLayoutControls where cmpFormLayoutSetID = ?', array($this->getComposerFormLayoutSetID()));
		if (!$displayOrder) {
			$displayOrder = 0;
		}
		$controlType = $control->getComposerControlTypeObject();
		$db->Execute('insert into ComposerFormLayoutControls (cmpFormLayoutSetID, cmpControlTypeID, cmpControlObject, cmpFormLayoutSetControlDisplayOrder) values (?, ?, ?, ?)', array(
			$this->getComposerFormLayoutSetID(), $controlType->getComposerControlTypeID(), serialize($control), $displayOrder
		));	
		return ComposerFormLayoutSetControl::getByID($db->Insert_ID());
	}

	public function rescanControlSetDisplayOrder() {
		$sets = ComposerFormLayoutSetControl::getList($this);
		$displayOrder = 0;
		foreach($controls as $control) {
			$control->updateFormLayoutSetControlDisplayOrder($displayOrder);
			$displayOrder++;
		}
	}


}