<?

defined('C5_EXECUTE') or die("Access Denied."); 

/**
 * An object that allows a filtered list of users to be returned.
 * @package Files 
 * @author Tony Trupp <tony@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

class Concrete5_Model_UserList extends DatabaseItemList { 

	protected $attributeFilters = array();
	protected $autoSortColumns = array('uName', 'uEmail', 'uDateAdded', 'uLastLogin', 'uNumLogins', 'uLastOnline');
	protected $itemsPerPage = 10;
	protected $attributeClass = 'UserAttributeKey';
	
	public $showInactiveUsers;
	public $showInvalidatedUsers=0;
	public $searchAgainstEmail=0;
	
	//Filter by uName
	public function filterByUserName($username) {
		$this->filter('u.uName', $username, '=');
	}
	
	public function filterByKeywords($keywords) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		$keys = UserAttributeKey::getSearchableIndexedList();
		$emailSearchStr=' OR u.uEmail like '.$qkeywords.' ';	
		$attribsStr = '';
		foreach ($keys as $ak) {
			$cnt = $ak->getController();			
			$attribsStr.=' OR ' . $cnt->searchKeywords($keywords);
		}
		$this->filter(false, '( u.uName like ' . $qkeywords . $emailSearchStr . $attribsStr . ')');
	}
	
	public function filterByGroup($groupName='', $inGroup = true){ 
		$group=Group::getByName($groupName); 
		$tbl='ug_'.$group->getGroupID();
		$this->addToQuery("left join UserGroups $tbl on {$tbl}.uID = u.uID ");	
		if ($inGroup) {
			$this->filter(false, "{$tbl}.gID=".intval($group->getGroupID()) );
		} else {
			$this->filter(false, "{$tbl}.gID is null");
		}
	}

	public function excludeUsers($uo) {
		if (is_object($uo)) {
			$uID = $uo->getUserID();
		} else {
			$uID = $uo;
		}
		$this->filter('u.uID',$uID,'!=');
	}

	public function filterByGroupID($gID){ 
		$tbl='ug_'.$gID;
		$this->addToQuery("left join UserGroups $tbl on {$tbl}.uID = u.uID ");			
		$this->filter(false, "{$tbl}.gID=".$gID);
	}

	public function filterByDateAdded($date, $comparison = '=') {
		$this->filter('u.uDateAdded', $date, $comparison);
	}
	
	// Returns an array of userInfo objects based on current filter settings
	public function get($itemsToGet = 100, $offset = 0) {
		$userInfos = array(); 
		$this->createQuery();
		$r = parent::get( $itemsToGet, intval($offset));
		foreach($r as $row) {
			$ui = UserInfo::getByID($row['uID']);			
			$userInfos[] = $ui;
		}
		return $userInfos;
	}	
	
	public function getTotal(){ 
		$this->createQuery();
		return parent::getTotal();
	}	
	
	public function filterByIsActive($val) {
		$this->showInactiveUsers = $val;
		$this->filter('u.uIsActive', $val);
	}	
	
	//this was added because calling both getTotal() and get() was duplicating some of the query components
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			if(!isset($this->showInactiveUsers)) $this->filter('u.uIsActive', 1);
			if(!$this->showInvalidatedUsers) $this->filter('u.uIsValidated', 0, '!=');
			$this->setupAttributeFilters("left join UserSearchIndexAttributes on (UserSearchIndexAttributes.uID = u.uID)");
			$this->queryCreated=1;
		}
	}	
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT DISTINCT u.uID, u.uName FROM Users u ');
	}

	/* magic method for filtering by page attributes. */
	public function __call($nm, $a) {
		if (substr($nm, 0, 8) == 'filterBy') {
			$txt = Loader::helper('text');
			$attrib = $txt->uncamelcase(substr($nm, 8));
			if (count($a) == 2) {
				$this->filterByAttribute($attrib, $a[0], $a[1]);
			} else {
				$this->filterByAttribute($attrib, $a[0]);
			}
		}			
	}

}

class UserSearchDefaultColumnSet extends DatabaseItemListColumnSet {
	protected $attributeClass = 'UserAttributeKey';	
	
	public function getUserName($ui) {
		return '<a href="' . View::url('/dashboard/users/search') . '?uID=' . $ui->getUserID() . '">' . $ui->getUserName() . '</a>';
	}

	public function getUserEmail($ui) {
		return '<a href="mailto:' . $ui->getUserEmail() . '">' . $ui->getUserEmail() . '</a>';
	}
	
	public static function getUserDateAdded($ui) {
		return date(DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS, strtotime($ui->getUserDateAdded()));
	}
	
	public function __construct() {
		$this->addColumn(new DatabaseItemListColumn('uName', t('Username'), array('UserSearchDefaultColumnSet', 'getUserName')));
		$this->addColumn(new DatabaseItemListColumn('uEmail', t('Email'), array('UserSearchDefaultColumnSet', 'getUserEmail')));
		$this->addColumn(new DatabaseItemListColumn('uDateAdded', t('Last Modified'), array('UserSearchDefaultColumnSet', 'getUserDateAdded')));
		$this->addColumn(new DatabaseItemListColumn('uNumLogins', t('# Logins'), 'getNumLogins')); 
		$date = $this->getColumnByKey('uDateAdded');
		$this->setDefaultSortColumn($date, 'desc');
	}
}

class UserSearchAvailableColumnSet extends UserSearchDefaultColumnSet {
	protected $attributeClass = 'UserAttributeKey';
	public function __construct() {
		parent::__construct();
	}
}

class UserSearchColumnSet extends DatabaseItemListColumnSet {
	protected $attributeClass = 'UserAttributeKey';
	public function getColumns() {
		$columns = array();
		$pk = PermissionKey::getByHandle('view_user_attributes');
		foreach($this->columns as $col) {
			if ($col instanceof DatabaseItemListAttributeKeyColumn) {
				$uk = $col->getAttributeKey();
				if ($pk->validate($uk)) {
					$columns[] = $col;
				}
			} else {
				$columns[] = $col;
			}
		}
		return $columns;
	}
	
	public function getCurrent() {
		$u = new User();
		$fldc = $u->config('USER_LIST_DEFAULT_COLUMNS');
		if ($fldc != '') {
			$fldc = @unserialize($fldc);
		}
		if (!($fldc instanceof DatabaseItemListColumnSet)) {
			$fldc = new UserSearchDefaultColumnSet();
		}
		return $fldc;
	}
}