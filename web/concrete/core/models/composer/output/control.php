<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerOutputControl extends Object {

	public function getComposerOutputControlID() {return $this->cmpOutputControlID;}
	public function getComposerOutputControlDisplayOrderID() {return $this->cmpOutputControlDisplayOrder;}
	public function getComposerFormLayoutSetControlID() {return $this->cmpFormLayoutSetControlID;}
	public function getComposerFormLayoutSetID() {return $this->cmpFormLayoutSetID;}

	public static function add(ComposerFormLayoutSetControl $control, PageTemplate $pt) {

		$set = $control->getComposerFormLayoutSetObject();
		$composer = $set->getComposerObject();

		$db = Loader::db();
		$db->Execute('insert into ComposerOutputControls (cmpID, pTemplateID, cmpFormLayoutSetControlID) values (?, ?, ?)', array(
			$composer->getComposerID(), $pt->getPageTemplateID(), $control->getComposerFormLayoutSetControlID()
		));
		$cmpOutputControlID = $db->Insert_ID();
		return ComposerOutputControl::getByID($cmpOutputControlID);
	}

	public static function getList(PageTemplate $pt) {
		$db = Loader::db();
		// get all composers.
		$cmpOutputControlIDs = $db->GetCol('select cmpOutputControlID from ComposerOutputControls where pTemplateID = ? order by cmpOutputControlDisplayOrder asc', array(
			$pt->getPageTemplateID()
		));
		$list = array();
		foreach($cmpOutputControlIDs as $cmpOutputControlID) {
			$cm = ComposerOutputControl::getByID($cmpOutputControlID);
			if (is_object($cm)) {
				$list[] = $cm;
			}
		}
		return $list;
	}

	public static function getByID($cmpOutputControlID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from ComposerOutputControls where cmpOutputControlID = ?', array($cmpOutputControlID));
		if (is_array($r) && $r['cmpOutputControlID']) {
			$cm = new ComposerOutputControl;
			$cm->setPropertiesFromArray($r);
			return $cm;
		}
	}

	public static function getByComposerFormLayoutSetControl(PageTemplate $pt, ComposerFormLayoutSetControl $control) {
		$db = Loader::db();
		$cmpOutputControlID = $db->GetOne('select cmpOutputControlID from ComposerOutputControls where pTemplateID = ? and cmpFormLayoutSetControlID = ?', array($pt->getPageTemplateID(), $control->getComposerFormLayoutSetControlID()));
		if ($cmpOutputControlID) {
			return ComposerOutputControl::getByID($cmpOutputControlID);
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from ComposerOutputControls where cmpOutputControlID = ?', array($this->cmpOutputControlID));
	}

	public function getComposerControlOutputLabel() {
		$control = ComposerFormLayoutSetControl::getByID($this->cmpFormLayoutSetControlID);
		return $control->getComposerControlLabel();
	}

}