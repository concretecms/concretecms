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
	
	public static function createDraft($ct) {
		$parent = Page::getByPath(COMPOSER_DRAFTS_PAGE_PATH);
		$p = $parent->add($ct, array());
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
		$u = new User();
		$drafts = Page::getByPath(COMPOSER_DRAFTS_PAGE_PATH);
		if ($entry->getCollectionParentID() != $drafts->getCollectionID()) {
			return false;
		}
		if ($u->getUserID() != $entry->getCollectionUserID()) {
			return false;
		}
		$cp = new Permissions($entry);
		if (!$cp->canRead()) {
			return false;
		}			
		return true;
	}
	
	public function markComposerPageAsSaved() {
		$db = Loader::db();
		$db->Replace('ComposerDrafts', array('cID' => $this->getCollectionID(), 'cpStatus' => self::COMPOSER_PAGE_STATUS_SAVED), array('cID'), true);
		$this->refreshCache();
	}
	
	public function getComposerBlocks() {
		// if the page is new and hasn't been saved, we get them directly from the master collection
		if ($this->cpStatus == self::COMPOSER_PAGE_STATUS_NEW) {
			$ct = CollectionType::getByID($this->getCollectionTypeID());
			return $ct->getCollectionTypeComposerBlocks();
		} else {
			// otherwise get the new copies of blocks on the current page which USED to be on the master collection and marked as included in composer
			$db = Loader::db();
			$q = "select cvb.bID, arHandle from CollectionVersionBlocks cvb inner join BlockRelations br on (cvb.bID = br.bID) inner join ComposerDefaultBlocks cdb on cdb.bID = br.originalBID where cvb.cID = ? and cvb.cvID = ?";
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
	
	public static function getByID($cID, $cvID = 'ACTIVE') {
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

