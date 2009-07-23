<?php 

defined('C5_EXECUTE') or die(_("Access Denied.")); 

/**
 * An object that allows a filtered list of users to be returned.
 * @package Files 
 * @author Tony Trupp <tony@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
Loader::model('userinfo');
Loader::model('groups');
Loader::model('user_attributes');

class UserList extends DatabaseItemList { 

	private $userAttributeFilters = array();
	protected $autoSortColumns = array('uName', 'uEmail', 'fvDateAdded', 'uLastLogin');
	protected $itemsPerPage = 10;
	public $keywordFilterAttrHandles=array();
	
	public $showInactiveUsers=0;
	public $showInvalidatedUsers=0;
	public $searchAgainstEmail=0;
	
	//Filter by uName
	public function filterByUserName($type) {
		$this->filter('u.uName', $type, '=');
	}
	
	// this lets you specify additional user attributes to search against
	public function addKeywordSearchAttributes($akHandle=''){
		$this->keywordFilterAttrHandles[]=$akHandle;
	}
	
	// Filters by "keywords"
	public function filterByKeywords($keywords) {
		$db = Loader::db();
		$keywordsExact = $db->quote($keywords);
		$keywords = $db->quote('%' . $keywords . '%');
		
		if($this->searchAgainstEmail){
			$emailSearchStr=' OR u.uEmail like '.$keywords.' ';
		}
		
		$i=0; 
		foreach($this->keywordFilterAttrHandles as $akHandle){
			$ukID = $db->GetOne("select ukID from UserAttributeKeys where ukHandle = ?", array($akHandle) );
			$tbl = "uavKeywords_{$i}";
			$this->addToQuery("left join UserAttributeValues $tbl on ({$tbl}.uID = u.uID and {$tbl}.ukID = {$ukID})");		
			$attrClauses.=" OR {$tbl}.value like ".$keywords;
			$i++;
		}		
		
		$this->filter(false, '( u.uName like ' . $keywords . $emailSearchStr . $attrClauses . ')');
	}
	
	/** 
	 * Filters the list by user attribute
	 * @param string $handle User Attribute Handle
	 * @param string $value
	 */
	public function filterByUserAttribute($handle, $value, $comparison = '=') {
		$ak = UserAttributeKey::getByHandle($handle);
		if(!$ak) {
			throw new Exception(t("The user attribute \"%s\" does not exist",$handle)); 
		}
		$this->userAttributeFilters[] = array($handle, $value, $comparison, $ak->getKeyType());
	}
	
	public function filterByGroup($groupName=''){ 
		$group=Group::getByName($groupName); 
		$tbl='ug_'.$group->getGroupID();
		$this->addToQuery("left join UserGroups $tbl on {$tbl}.uID = u.uID ");			
		$this->filter(false, "{$tbl}.gID=".intval($group->getGroupID()) );
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
	
	//this was added because calling both getTotal() and get() was duplicating some of the query components
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			if( !$this->showInactiveUsers) $this->filter('u.uIsActive', 1);
			if( !$this->showInvalidatedUsers) $this->filter('u.uIsValidated', 0, '!=');
			$this->setupAttributeFilters();
			//$this->setupFilePermissions();			
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
				$this->filterByUserAttribute($attrib, $a[0], $a[1]);
			} else {
				$this->filterByUserAttribute($attrib, $a[0]);
			}
		}			
	}
	
	protected function setupAttributeFilters() {
		$db = Loader::db();
		$i = 1;
		foreach($this->userAttributeFilters as $caf) {
			$ukID = $db->GetOne("select ukID from UserAttributeKeys where ukHandle = ?", array($caf[0]));
			$tbl = "uav_{$i}";
			$this->addToQuery("left join UserAttributeValues $tbl on ({$tbl}.uID = u.uID and {$tbl}.ukID = {$ukID})");
			switch($caf[3]) { 		
				case 'NUMBER':
					$val = $db->quote($caf[1]);
					$this->filter(false, 'CAST(' . $tbl . '.value as unsigned) ' . $caf[2] . ' ' . $val);
					break;
				case 'DATE':
					$val = $db->quote($caf[1]);
					$this->filter(false, 'CAST(' . $tbl . '.value as date) ' . $caf[2] . ' ' . $val);
					break;
				case 'SELECT_MULTIPLE':
					$multiString = '(';
					$i = 0;
					if(!is_array($caf[1])) $caf[1]=array($caf[1]); 
					foreach($caf[1] as $val) {
						$val = $db->quote('%' . $val . '||%');
						$multiString .= 'REPLACE(' . $tbl . '.value, "\n", "||") like ' . $val . ' ';
						if (($i + 1) < count($caf[1])) {
							$multiString .= 'OR ';
						}
						$i++;
					}
					$multiString .= ')';
					$this->filter(false, $multiString);
					break;
				case 'TEXT':
					$val = $db->quote($caf[1]);
					$this->filter(false, $tbl . '.value ' . $caf[2] . ' ' . $val);
					break;
				default:
					$this->filter($tbl . '.value', $caf[1], $caf[2]);
					break;
			}
			$i++;
		}
	}
	

}

?>