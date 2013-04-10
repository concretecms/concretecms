<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerDraft extends Object {

	protected $c;
	protected $cmpVersionsToSave = 10;

	public function getComposerDraftID() {return $this->cmpDraftID;}
	public function getComposerID() {return $this->cmpID;}
	public function getComposerObject() {
		return Composer::getByID($this->cmpID);
	}
	public function getComposerDraftDateCreated() {return $this->cmpDateCreated;}
	public function getComposerDraftUserID() {return $this->uID;}
	public function getComposerDraftCollectionID() {return $this->cID;}
	public function getComposerDraftCollectionObject() {
		if (!isset($this->c)) {
			$this->c = Page::getByID($this->cID);
		}
		if (is_object($this->c) && !$this->c->isError()) {
			return $this->c;
		}
	}

	public function createNewCollectionVersion() {
		$c = $this->getComposerDraftCollectionObject();
		$this->c = $c->cloneVersion('');
	}

	public function saveForm() {
		$controls = ComposerControl::getList($this->getComposerObject());
		$outputControls = array();
		foreach($controls as $cn) {
			$data = $cn->getRequestValue();
			$cn->publishToPage($this, $data, $controls);
			$outputControls[] = $cn;
		}
		$this->setPageNameFromComposerControls($outputControls);

		// remove all but the most recent X drafts.
		$vl = new VersionList($this->getComposerDraftCollectionObject(), -1);
		// this will ensure that we only ever keep X versions.
		$vArray = $vl->getVersionListArray();
		if (count($vArray) > $this->cmpVersionsToSave) {
			for ($i = $this->cmpVersionsToSave; $i < count($vArray); $i++) {
				$v = $vArray[$i];
				@$v->delete();
			} 
		}

		return $outputControls;
	}

	public function getComposerDraftTargetParentPageID() {return $this->cmpDraftTargetParentPageID;}
	
	public static function getByID($cmpDraftID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from ComposerDrafts where cmpDraftID = ?', array($cmpDraftID));
		if (is_array($r) && $r['cmpDraftID']) {
			$cm = new ComposerDraft;
			$cm->setPropertiesFromArray($r);
			return $cm;
		}
	}

	public function discard() {
		$c = $this->getComposerDraftCollectionObject();
		$c->delete();
		$db = Loader::db();
		$db->Execute('delete from ComposerDrafts where cmpDraftID = ?', array($this->cmpDraftID));
		$db->Execute('delete from ComposerDraftBlocks where cmpDraftID = ?', array($this->cmpDraftID));
	}

	public function getMyDrafts() {
		$db = Loader::db();
		$u = new User();
		$r = $db->Execute('select ComposerDrafts.cmpDraftID from ComposerDrafts where uID = ? order by cmpDateCreated desc', array($u->getUserID()));
		$pages = array();
		while ($row = $r->FetchRow()) {
			$entry = ComposerDraft::getByID($row['cmpDraftID']);
			if (is_object($entry)) {
				$pages[] = $entry;
			}
		}
		return $pages;		
	}

	public function setComposerDraftTargetParentPageID($cParentID) {
		$db = Loader::db();
		$db->Execute('update ComposerDrafts set cmpDraftTargetParentPageID = ? where cmpDraftID = ?', array($cParentID, $this->cmpDraftID));
		$this->cmpDraftTargetParentPageID = $cParentID;
	}

	public function setPageNameFromComposerControls($controls) {
		$dc = $this->getComposerDraftCollectionObject();
		// now we see if there's a page name field in there
		$containsPageNameControl = false;
		foreach($controls as $cn) {
			if ($cn instanceof NameCorePagePropertyComposerControl) {
				$containsPageNameControl = true;
				break;
			}
		}
		if (!$containsPageNameControl) {
			foreach($controls as $cn) {
				if ($cn->canComposerControlSetPageName()) {
					$pageName = $cn->getComposerControlPageNameValue($dc);
					$dc->updateCollectionName($pageName);
				}
			}
		}
	}		

	public function publish() {
		$parent = Page::getByID($this->cmpDraftTargetParentPageID);
		$c = $this->getComposerDraftCollectionObject();
		$c->move($parent);
		$u = new User();

		$v = CollectionVersion::get($c, 'RECENT');
		$pkr = new ApprovePagePageWorkflowRequest();
		$pkr->setRequestedPage($c);
		$pkr->setRequestedVersionID($v->getVersionID());
		$pkr->setRequesterUserID($u->getUserID());
		$pkr->trigger();

		$c->activate();
		$db = Loader::db();
		$db->Execute('delete from ComposerDrafts where cmpDraftID = ?', array($this->cmpDraftID));

		Events::fire('on_composer_draft_publish', $this);

		CacheLocal::flush();
	}
}