<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerDraft extends Object {

	public function getComposerDraftID() {return $this->cmpDraftID;}
	public function getComposerID() {return $this->cmpID;}
	public function getComposerDraftDateCreated() {return $this->cmpDateCreated;}
	public function getComposerDraftUserID() {return $this->uID;}
	public function getComposerDraftCollectionID() {return $this->cID;}
	public function getComposerDraftCollectionObject() {
		$c = Page::getByID($this->cID);
		if (is_object($c) && !$c->isError()) {
			return $c;
		}
	}
	public static function getByID($cmpDraftID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from ComposerDrafts where cmpDraftID = ?', array($cmpDraftID));
		if (is_array($r) && $r['cmpDraftID']) {
			$cm = new ComposerDraft;
			$cm->setPropertiesFromArray($r);
			return $cm;
		}
	}

}