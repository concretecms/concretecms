<?

defined('C5_EXECUTE') or die("Access Denied.");

class Stack extends Page {

	protected static function isValidStack($stack) {
		$parent = Page::getByPath(STACKS_PAGE_PATH);
		if ($stack->getCollectionParentID() != $parent->getCollectionID()) {
			return false;
		}
		
		$cp = new Permissions($stack);
		if (!$cp->canRead()) {
			return false;
		}			
		return true;
	}

	public static function addStack($stackName) {
		$ct = new CollectionType();
		$data = array();

		$parent = Page::getByPath(STACKS_PAGE_PATH);
		$data = array();
		$data['name'] = $stackName;
		if (!$stackName) {
			$data['name'] = t('No Name');
		}
		$data['filename'] = STACKS_PAGE_FILENAME;
		$page = $parent->addStatic($data);	

		// we have to do this because we need the area to exist before we try and add something to it.
		$a = Area::getOrCreate($page,  STACKS_AREA_NAME);
	}
	
	public static function getByName($stackName, $cvID = 'RECENT') {
		$db = Loader::db();
		$parent = Page::getByPath(STACKS_PAGE_PATH);
		$cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions on Pages.cID = CollectionVersions.cID where cParentID = ? and cvName = ? order by cvID desc', array($parent->getCollectionID(), $stackName));
		if ($cID) {
			return self::getByID($cID, $cvID);
		}
	}
	
	public static function getOrCreate($stackName) {
		$stack = self::getByName($stackName);
		if (!$stack) {		
			$stack = self::addStack($stackName);
		}
		return $stack;
	}
	
	public static function getByID($cID, $cvID = 'RECENT') {
		$db = Loader::db();
		$c = parent::getByID($cID, $cvID, 'Stack');

		if (self::isValidStack($c)) {
			return $c;
		}
		return false;
	}

}
