<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_Stack extends Page {

	const ST_TYPE_USER_ADDED = 0;
	const ST_TYPE_GLOBAL_AREA = 20;
	
	public function getStackName() {
		$db = Loader::db();
		return $db->GetOne('select stName from Stacks where cID = ?', array($this->getCollectionID()));
	}
	
	public function getStackType() {
		$db = Loader::db();
		return $db->GetOne('select stType from Stacks where cID = ?', array($this->getCollectionID()));
	}
	
	public function getStackTypeExportText() {
		switch($this->getStackType()) {
			case self::ST_TYPE_GLOBAL_AREA:
				return 'global_area';
				break;
			default: 
				return false;
				break;
		}
	}
	
	public static function mapImportTextToType($type) {
		switch($type) {
			case 'global_area':
				return self::ST_TYPE_GLOBAL_AREA;
				break;
			default:
				return self::ST_TYPE_USER_ADDED;
				break;
		}		
	}
	
	protected static function isValidStack($stack) {
		$parent = Page::getByPath(STACKS_PAGE_PATH);
		if ($stack->getCollectionParentID() != $parent->getCollectionID()) {
			return false;
		}
		
		return true;
	}

	public static function addStack($stackName, $type = self::ST_TYPE_USER_ADDED) {
		$ct = new CollectionType();
		$data = array();

		$parent = Page::getByPath(STACKS_PAGE_PATH);
		$data = array();
		$data['name'] = $stackName;
		if (!$stackName) {
			$data['name'] = t('No Name');
		}
		$pagetype = CollectionType::getByHandle(STACKS_PAGE_TYPE);
		$page = $parent->add($pagetype, $data);	

		// we have to do this because we need the area to exist before we try and add something to it.
		$a = Area::getOrCreate($page, STACKS_AREA_NAME);
		
		// finally we add the row to the stacks table
		$db = Loader::db();
		$stackCID = $page->getCollectionID();
		$v = array($stackName, $stackCID, $type);
		$db->Execute('insert into Stacks (stName, cID, stType) values (?, ?, ?)', $v);
		
		//Return the new stack
		return self::getByID($stackCID);
	}
	
	public static function getByName($stackName, $cvID = 'RECENT') {
		$cID = CacheLocal::getEntry('stack_by_name', $stackName);
		if (!$cID) {
			$db = Loader::db();
			$cID = $db->GetOne('select cID from Stacks where stName = ?', array($stackName));
			CacheLocal::set('stack_by_name', $stackName, $cID);
		}
		
		if ($cID) {
			return self::getByID($cID, $cvID);
		}
	}
	
	public function delete() {
		if ($this->getStackType() == self::ST_TYPE_GLOBAL_AREA) {
			GlobalArea::deleteByName($this->getStackName());
		}

		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from Stacks where cID = ?', array($this->getCollectionID()));
	}

	public function display() {
		$ax = Area::get($this, STACKS_AREA_NAME);
		$ax->display($this);
	}
	
	public static function getOrCreateGlobalArea($stackName) {
		$stack = self::getByName($stackName);
		if (!$stack) {		
			$stack = self::addStack($stackName, self::ST_TYPE_GLOBAL_AREA);
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

	public function export($pageNode) {

		$p = $pageNode->addChild('stack');
		$p->addAttribute('name', Loader::helper('text')->entities($this->getCollectionName()));
		if ($this->getStackTypeExportText()) {
			$p->addAttribute('type', $this->getStackTypeExportText());
		}
		
		$db = Loader::db();
		$r = $db->Execute('select arHandle from Areas where cID = ?', array($this->getCollectionID()));
		while ($row = $r->FetchRow()) {
			$ax = Area::get($this, $row['arHandle']);
			$ax->export($p, $this);
		}
	}

}
