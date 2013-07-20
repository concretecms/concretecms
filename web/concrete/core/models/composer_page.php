<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
*
* ComposerPage is a special object of pages that haven't been published yet but still live in the sitemap system.
* @package Pages
*
*/
Loader::model('collection_types');
class Concrete5_Model_ComposerPage extends Page {
	
	public static function createDraft($ct) {
		$parent = Page::getByPath(COMPOSER_DRAFTS_PAGE_PATH);
		$data['cvIsApproved'] = 0;
		$p = $parent->add($ct, $data);
		$p->deactivate();
				
		$db = Loader::db();
		$targetPageID = 0;
		if ($ct->getCollectionTypeComposerPublishMethod() == 'PARENT') {
			$targetPageID = $ct->getCollectionTypeComposerPublishPageParentID();			
		}
		$db->Execute('insert into ComposerDrafts (cID, cpPublishParentID) values (?, ?)', array($p->getCollectionID(), $targetPageID));
		$entry = ComposerPage::getByID($p->getCollectionID());
		if (is_object($entry)) {
			// duplicate all composer blocks onto the new page and make them into new blocks
			$blocks = $entry->getComposerBlocks();
			foreach($blocks as $b) {
				$b2 = Block::getByID($b->getBlockID(), $p, $b->getAreaHandle());
				$nb = $b2->duplicate($p);
				$b2->deleteBlock();
				$b2 = $nb;
			}					
			
			return $entry;		
		}
	}
	
	/** 
	 * Checks to see if the page in question is a valid composer draft for the logged in user
	 */
	protected static function isValidComposerPage($entry) {
		$ct = CollectionType::getByID($entry->getCollectionTypeID());
		if (!is_object($ct) || !$ct->isCollectionTypeIncludedInComposer()) {
			return false;
		}
		$cp = new Permissions($entry);
		if (!$cp->canEditPageContents()) {
			return false;
		}			
		return true;
	}
	
	public function isComposerDraft() {
		$db = Loader::db();
		$cID = $db->GetOne('select cID from ComposerDrafts where cID = ?', array($this->getCollectionID()));
		return $cID == $this->getCollectionID();
	}
	
	public function getComposerDraftPublishParentID() {
		if ($this->cpPublishParentID > 0) {
			$pc = Page::getByID($this->cpPublishParentID);
			if (is_object($pc) && !$pc->isError() && !$pc->isInTrash()) {
				return $this->cpPublishParentID;
			}
		}
	}

	public function setComposerDraftPublishParentID($cParentID) {
		$this->cpPublishParentID = $cParentID;
		$db = Loader::db();
		$db->Execute('update ComposerDrafts set cpPublishParentID = ? where cID = ?', array($cParentID, $this->getCollectionID()));
	}
	
	// old
	/*
	public function isValidComposerDraft() {
		$u = new User();
		if ($this->getComposerPageStatus() >= ComposerPage::COMPOSER_PAGE_STATUS_PUBLISHED) {
			return false;
		}
		if ($u->getUserID() != $this->getCollectionUserID()) {
			return false;
		}
		return self::isValidComposerPage($this);
	}
	
	public function markComposerPageAsSaved() {
		$db = Loader::db();
		$db->Replace('ComposerDrafts', array('cID' => $this->getCollectionID(), 'cpStatus' => self::COMPOSER_PAGE_STATUS_SAVED), array('cID'), true);
		$this->refreshCache();
	}

	*/

	public function markComposerPageAsPublished() {
		$db = Loader::db();
		$db->Execute('delete from ComposerDrafts where cID = ?', array($this->getCollectionID()));
		$this->activate();
	}

	public function getMyDrafts() {
		$db = Loader::db();
		$u = new User();
		$r = $db->Execute('select ComposerDrafts.cID from ComposerDrafts inner join Pages on ComposerDrafts.cID = Pages.cID inner join Collections on Collections.cID = Pages.cID where uID = ? order by cDateModified desc', array($u->getUserID()));
		$pages = array();
		while ($row = $r->FetchRow()) {
			$entry = ComposerPage::getByID($row['cID']);
			if (is_object($entry)) {
				$pages[] = $entry;
			}
		}
		return $pages;		
	}
	
	
	
	public function getComposerBlocks() {
		$db = Loader::db();
		$bIDs = $db->GetCol('select bID from ComposerContentLayout where bID > 0 and ctID = ? order by displayOrder', array($this->getCollectionTypeID()));
		$blocks = array();
		foreach($bIDs as $bID) {
			$b = Block::getByID($bID);
			$b = $this->getComposerBlockInstance($b);
			if (is_object($b)) {
				$blocks[] = $b;
			}
		}
		return $blocks;
	}
	
	public static function getByID($cID, $cvID = 'RECENT') {
		$db = Loader::db();
		$c = parent::getByID($cID, $cvID, 'ComposerPage');

		$r = $db->GetRow('select cpPublishParentID from ComposerDrafts where cID = ?', array($c->getCollectionID()));
		$c->cpPublishParentID = $r['cpPublishParentID'];

		if (self::isValidComposerPage($c)) {
			return $c;
		}
		return false;
	}
	
	public function getComposerBlockInstance($b) {
		// this gets a master collection block and finds the current block in the current entry that matches it, complete with area name, etc...
		$db = Loader::db();
		// is the block in the current page with the current id ?
		$arHandle = $db->getOne('select arHandle from CollectionVersionBlocks where cID = ? and cvID = ? and bID = ?', array($this->getCollectionID(), $this->getVersionID(), $b->getBlockID()));
		if (!$arHandle) {
			$tempBID = $b->getBlockID();
			while ($tempBID != false) {
				$bID = $tempBID;
				$tempBID = $db->GetOne('select distinct br.bID from BlockRelations br inner join CollectionVersionBlocks cvb on cvb.bID = br.bID where br.originalBID = ? and cvb.cID = ? and cvb.cvID = ?', array($bID, $this->getCollectionID(),  $this->getVersionID()));
			}
			$arHandle = $db->getOne('select arHandle from CollectionVersionBlocks where cID = ? and cvID = ? and bID = ?', array($this->getCollectionID(), $this->getVersionID(), $bID));
		} else {
			$bID = $b->getBlockID();
		}
		
		if ($arHandle) {
			$c = Page::getByID($this->getCollectionID(), $this->getVersionID());
			$b = Block::getByID($bID, $c, $arHandle);
			return $b;
		}
		
	}

}

