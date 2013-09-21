<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageDraft extends Object {

	protected $c;
	protected $pDraftVersionsToSave = 10;

	public function getPageDraftID() {return $this->pDraftID;}
	public function getPageTypeID() {return $this->ptID;}
	public function getPageTypeObject() {
		return PageType::getByID($this->ptID);
	}
	public function getPageDraftDateCreated() {return $this->pDraftDateCreated;}
	public function getPageDraftUserID() {return $this->uID;}
	public function getPageDraftCollectionID() {return $this->cID;}
	public function getPageDraftCollectionObject() {
		if (!isset($this->c)) {
			$this->c = Page::getByID($this->cID);
		}
		if (is_object($this->c) && !$this->c->isError()) {
			return $this->c;
		}
	}
	public function overridePageTypePermissions() {
		return $this->pDraftOverridePageTypePermissions;
	}
	public function createNewCollectionVersion() {
		$c = $this->getPageDraftCollectionObject();
		$this->c = $c->cloneVersion('');
	}

	public function saveForm() {
		$controls = PageTypeComposerControl::getList($this->getPageTypeObject());
		$outputControls = array();
		foreach($controls as $cn) {
			$data = $cn->getRequestValue();
			$cn->publishToPage($this, $data, $controls);
			$outputControls[] = $cn;
		}
		$this->setPageNameFromPageTypeComposerControls($outputControls);

		// remove all but the most recent X drafts.
		$vl = new VersionList($this->getPageDraftCollectionObject(), -1);
		// this will ensure that we only ever keep X versions.
		$vArray = $vl->getVersionListArray();
		if (count($vArray) > $this->pDraftVersionsToSave) {
			for ($i = $this->pDraftVersionsToSave; $i < count($vArray); $i++) {
				$v = $vArray[$i];
				@$v->delete();
			} 
		}

		return $outputControls;
	}

	public function getPermissionObjectIdentifier() {return $this->pDraftID;}
	public function getPageDraftTargetParentPageID() {return $this->pDraftPublishTargetParentPageID;}
	
	public static function getByID($pDraftID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from PageDrafts where pDraftID = ?', array($pDraftID));
		if (is_array($r) && $r['pDraftID']) {
			$cm = new PageDraft;
			$cm->setPropertiesFromArray($r);
			return $cm;
		}
	}

	public function resetPageDraftPermissions() {
		$db = Loader::db();
		$db->Execute("delete from PageDraftPermissionAssignments where pDraftID = ?", array($this->pDraftID));
		$db->Execute("update PageDrafts set pDraftOverridePageTypePermissions = 0 where pDraftID = ?", array($this->pDraftID));
	}

	public function doOverridePageTypePermissions() {
		$db = Loader::db();
		$db->Execute("delete from PageDraftPermissionAssignments where pDraftID = ?", array($this->pDraftID));
		$db->Execute("update PageDrafts set pDraftOverridePageTypePermissions = 1 where pDraftID = ?", array($this->pDraftID));
		$permissions = PermissionKey::getList('page_draft');
		foreach($permissions as $pk) { 
			$pk->setPermissionObject($this);
			$pk->copyFromPageTypeToPageDraft();
		}
	}

	public function discard() {
		$c = $this->getPageDraftCollectionObject();
		$c->delete();
		$db = Loader::db();
		$db->Execute('delete from PageDrafts where pDraftID = ?', array($this->pDraftID));
		$db->Execute('delete from PageDraftBlocks where pDraftID = ?', array($this->pDraftID));
	}

	public function getList() {
		$db = Loader::db();
		$u = new User();
		$r = $db->Execute('select pDraftID from PageDrafts order by pDraftDateCreated desc');
		$pages = array();
		while ($row = $r->FetchRow()) {
			$entry = PageDraft::getByID($row['pDraftID']);
			if (is_object($entry)) {
				$pages[] = $entry;
			}
		}
		return $pages;		
	}

	public function setPageDraftTargetParentPageID($cParentID) {
		$db = Loader::db();
		$db->Execute('update PageDrafts set pDraftPublishTargetParentPageID = ? where pDraftID = ?', array($cParentID, $this->pDraftID));
		$this->pDraftPublishTargetParentPageID = $cParentID;
	}

	public function setPageNameFromPageTypeComposerControls($controls) {
		$dc = $this->getPageDraftCollectionObject();
		// now we see if there's a page name field in there
		$containsPageNameControl = false;
		foreach($controls as $cn) {
			if ($cn instanceof NameCorePagePropertyPageTypeComposerControl) {
				$containsPageNameControl = true;
				break;
			}
		}
		if (!$containsPageNameControl) {
			foreach($controls as $cn) {
				if ($cn->canPageTypeComposerControlSetPageName()) {
					$pageName = $cn->getPageTypeComposerControlPageNameValue($dc);
					$dc->updateCollectionName($pageName);
				}
			}
		}
	}			

	protected function stripEmptyPageTypeComposerControls() {
		$controls = PageTypeComposerControl::getList($this->getPageTypeObject());
		foreach($controls as $cn) {			
			$cn->setPageDraftObject($this);
			if ($cn->shouldPageTypeComposerControlStripEmptyValuesFromDraft() && $cn->isPageTypeComposerControlDraftValueEmpty()) {
				$cn->removePageTypeComposerControlFromDraft();
			}
		}
	}

	public function publish() {
		$this->stripEmptyPageTypeComposerControls();

		$parent = Page::getByID($this->pDraftPublishTargetParentPageID);
		$c = $this->getPageDraftCollectionObject();
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
		$db->Execute('delete from PageDrafts where pDraftID = ?', array($this->pDraftID));

		Events::fire('on_page_draft_publish', $this);

		CacheLocal::flush();
	}
}