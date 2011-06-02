<?

defined('C5_EXECUTE') or die("Access Denied.");
class LanguageSectionPage extends Page {

	public static function assign($c, $language, $icon) {
		$db = Loader::db();
		$db->Replace('LanguageSectionPages', array('cID' => $c->getCollectionID(), 'lsLanguage' => $language, 'lsIcon' => $icon), array('cID'), true);
	}

	public static function getByID($cID, $cvID = 'RECENT') {
		$r = self::isLanguageSectionPage($cID);
		if ($r) {
			$obj = parent::getByID($cID, $cvID, 'LanguageSectionPage');
			$obj->lsLanguage = $r['lsLanguage'];
			$obj->lsIcon = $r['lsIcon'];
			return $obj;
		}

		return false;
	}
	
	public function getLanguage() {return $this->lsLanguage;}
	public function getIcon() {return $this->lsIcon;}
	
	public static function isLanguageSectionPage($cID) {
		if (is_object($cID)) {
			$cID = $cID->getCollectionID();
		}
		$db = Loader::db();
		$r = $db->GetRow('select cID, lsLanguage, lsIcon from LanguageSectionPages where cID = ?', array($cID));
		if ($r && is_array($r) && $r['lsLanguage']) {
			return $r;
		} else {
			return false;
		}		
	}
	
	public static function getList() {
		$db = Loader::db();
		$r = $db->Execute('select cID from LanguageSectionPages order by lsLanguage asc');
		$pages = array();
		while ($row = $r->FetchRow()) {
			$obj = self::getByID($row['cID']);
			if (is_object($obj)) {
				$pages[] = $obj;
			}
		}
		return $pages;
	}

}
