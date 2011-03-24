<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
*
* ComposerPage is a special object of pages that haven't been published yet but still live in the sitemap system.
* @package Pages
*
*/
Loader::model('collection_types');
class ComposerPage extends Page {
	
	const COMPOSER_PAGE_STATUS_NEW = 1;
	const COMPOSER_PAGE_STATUS_SAVED = 2;
	const COMPOSER_PAGE_STATUS_PUBLISHED = 10;
	
	public static function createDraft($ct) {
		$parent = Page::getByPath(COMPOSER_DRAFTS_PAGE_PATH);
		$data['cvIsApproved'] = 0;
		$p = $parent->add($ct, $data);
				
		$db = Loader::db();
		$db->Execute('insert into ComposerDrafts (cID, cpStatus) values (?, ?)', array($p->getCollectionID(), self::COMPOSER_PAGE_STATUS_NEW));
		$entry = ComposerPage::getByID($p->getCollectionID());

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
	
	public function getComposerPageStatus() {
		return $this->cpStatus;
	}

	/** 
	 * Checks to see if the page in question is a valid composer draft for the logged in user
	 */
	protected static function isValidComposerPage($entry) {
		$ct = CollectionType::getByID($entry->getCollectionTypeID());
		if (!$ct->isCollectionTypeIncludedInComposer()) {
			return false;
		}
		$cp = new Permissions($entry);
		if (!$cp->canWrite()) {
			return false;
		}			
		return true;
	}
	
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

	public function markComposerPageAsPublished() {
		$db = Loader::db();
		$db->Replace('ComposerDrafts', array('cID' => $this->getCollectionID(), 'cpStatus' => self::COMPOSER_PAGE_STATUS_PUBLISHED), array('cID'), true);
		$this->refreshCache();
	}
	
	public function getMyDrafts() {
		$db = Loader::db();
		$u = new User();
		$r = $db->Execute('select ComposerDrafts.cID from ComposerDrafts inner join Pages on ComposerDrafts.cID = Pages.cID inner join Collections on Collections.cID = Pages.cID where cpStatus <> ? and uID = ? order by cDateModified desc', array(self::COMPOSER_PAGE_STATUS_PUBLISHED, $u->getUserID()));
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
		$tblocks = $this->getBlocks();
		$blocks = array();
		foreach($tblocks as $b) {
			if ($b->isBlockIncludedInComposer()) {
				$blocks[] = $b;
			}
		}
		return $blocks;
	}
	
	public static function getByID($cID, $cvID = 'RECENT') {
		$db = Loader::db();
		$c = parent::getByID($cID, $cvID, 'ComposerPage');
		$r = $db->GetRow('select cpStatus from ComposerDrafts where cID = ?', array($c->getCollectionID()));
		if ($r['cpStatus'] > 0) {
			$c->cpStatus = $r['cpStatus'];
		}
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
		if ($arHandle) {
			$c = Page::getByID($this->getCollectionID(), $this->getVersionID());
			$b = Block::getByID($b->getBlockID(), $c, $arHandle);
			return $b;
		}
		
	}

}

