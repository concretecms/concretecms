<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
*
* ComposerPage is a special object of pages that haven't been published yet but still live in the sitemap system.
* @package Pages
*
*/
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
		return ComposerPage::getByID($p->getCollectionID());
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
		// if the page is new and hasn't been saved, we get them directly from the master collection
		if ($this->cpStatus == self::COMPOSER_PAGE_STATUS_NEW) {
			$ct = CollectionType::getByID($this->getCollectionTypeID());
			return $ct->getCollectionTypeComposerBlocks();
		} else {
			// otherwise get the new copies of blocks on the current page which USED to be on the master collection and marked as included in composer
			$db = Loader::db();
			$q = "select cvb.bID, cvb.arHandle from CollectionVersionBlocks cvb inner join BlockRelations br on (cvb.bID = br.bID) inner join ComposerDefaultBlocks cdb on cdb.bID = br.originalBID inner join CollectionVersionBlocks cvb2 on cdb.bID = cvb2.bID where cvb.cID = ? and cvb.cvID = ? order by cvb2.cbDisplayOrder";
			$v = array($this->getCollectionID(), $this->getVersionID());
			$r = $db->Execute($q, $v);
			$blocks = array();
			while ($row = $r->FetchRow()) {
				$b = Block::getByID($row['bID'], $this, $row['arHandle']);
				if (is_object($b)) {
					$blocks[] = $b;
				}
			}
			return $blocks;
		}
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

}

