<?php 
/**
*
* A wrapper class for results from the search engine, allowing for abstraction in case search engines are changed in the future.
* @package Utilities
* @subpackage Search
*/
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('page_list');

class IndexedSearchResult {


	public function __construct($id, $name, $description, $score, $cPath, $content) {
		$this->cID = $id;
		$this->cName = $name;
		$this->cDescription = $description;		
		$this->score = $score;
		$this->cPath = $cPath;
		$this->content = $content;
	}

	public function getID() {return $this->cID;}
	public function getName() {return $this->cName;}
	public function getDescription() {return $this->cDescription;}
	public function getScore() {return $this->score;}
	public function getCollectionPath() {return $this->cPath;}
	public function getCpath() {return $this->cPath;}
	public function getBodyContent() {return $this->content;}
}

class IndexedPageList extends DatabaseItemList {

	protected $itemsPerPage = 10;
	
	public function filterByKeywordsBoolean($kw) {
		return $this->filterByKeywords($kw);
	}
	
	public function filterByKeywords($kw) {
		$db = Loader::db();
		$kw = $db->quote($kw);
		$this->addToQuery("select PageSearchIndex.*, match(cName, cDescription, content) against ({$kw} in boolean mode) as score from PageSearchIndex inner join Pages on PageSearchIndex.cID = Pages.cID inner join CollectionSearchIndexAttributes on PageSearchIndex.cID = CollectionSearchIndexAttributes.cID");
		Loader::model('attribute/categories/collection');
		
		$keys = CollectionAttributeKey::getSearchableIndexedList();
		$attribsStr = '';
		foreach ($keys as $ak) {
			$attribsStr=' OR ak_' . $ak->getAttributeKeyHandle() . ' like '.$kw.' ';	
		}

		$this->filter(false, "(match(cName, cDescription, content) against ({$kw} in boolean mode) {$attribsStr})");

	}
	
	private $searchPaths = array();
	
	public function addSearchPath($path) {
		$this->searchPaths[] = $path;
	}

	public function setupPermissions() {
		$u = new User();
		if ($u->isSuperUser()) {
			return; // super user always sees everything. no need to limit
		}
		$groups = $u->getUserGroups();
		$groupIDs = array();
		foreach($groups as $key => $value) {
			$groupIDs[] = $key;
		}
		
		$uID = -1;
		if ($u->isRegistered()) {
			$uID = $u->getUserID();
		}

		$this->addToQuery('left join PagePermissions pp1 on (pp1.cID = Pages.cInheritPermissionsFromCID)');
		$this->filter(false, "(pp1.cgPermissions like 'r%' and (pp1.gID in (" . implode(',', $groupIDs) . ") or pp1.uID = {$uID}))");
	}
	
	public function getPage() {
		$db = Loader::db(); 
		$this->setupPermissions();

		if (count($this->searchPaths) > 0) { 
			$i = 0;
			$subfilter = '';
			foreach($this->searchPaths as $sp) {
				if ($sp == '') {
					continue;
				}
				$sp = $db->quote($sp . '%');
				$subfilter .= "cPath like {$sp} ";
				if (($i+1) < count($this->searchPaths)) {
					$subfilter .= "or ";
				}
				$i++;
			}
			if ($subfilter != '') {
				$this->filter(false, $subfilter);
			}
		}

		$this->sortByMultiple('score desc', 'cDatePublic desc');
		return parent::getPage();
	}
}

/**
*
* A wrapper class for the search engine that Concrete integrates
* @package Utilities
* @subpackage Search
*/
class IndexedSearch {
	
	private $cPathSections = array();
	private $searchableAreaNames = array('Main Content', 'Main');
	
	public function addSearchableArea($arr) {
		$this->searchableAreaNames[] = $arr;
	}
	
	public function getBodyContentFromPage($c) {
		$searchableAreaNames=$this->searchableAreaNames;
		$blarray=array();
		foreach($searchableAreaNames as $searchableAreaName){
		 	$blarray = array_merge( $blarray, $c->getBlocks($searchableAreaName) );
		}
		$text = '';
		$tagsToSpaces=array('<br>','<br/>','<br />','<p>','</p>','</ p>','<div>','</div>','</ div>');
		foreach($blarray as $b) { 
			$bi = $b->getInstance();
			if(method_exists($bi,'getSearchableContent')){
				$searchableContent = $bi->getSearchableContent();  
				if(strlen(trim($searchableContent))) 					
					$text .= strip_tags(str_ireplace($tagsToSpaces,' ',$searchableContent)).' ';
			}			
		}
		unset($blarray);
		return $text;
	}
	
	/** 
	 * Reindexes the search engine.
	 */
	public function reindexAll() {
		Cache::disableLocalCache();
		
		$db = Loader::db();
		Loader::model('collection_attributes');
		$r = $db->query("select cID from Pages order by cID asc");
		$nh = Loader::helper('navigation');
		
		$db->Execute("truncate table PageSearchIndex");
		
		$num = 0;
		while ($row = $r->fetchRow()) {
			$c = Page::getByID($row['cID'], 'ACTIVE');
			
			if ($c->isSystemPage() || $c->getCollectionAttributeValue('exclude_search_index')) {
				continue;
			}
			
			// make sure something is approved
			$cv = $c->getVersionObject();
			if(!$cv->cvIsApproved) { 
				continue;
			}		
			
			$c->reindex();
			$num++;
		
			unset($c);
		}
		
		$r->Close();
		Cache::enableLocalCache();
		$result = new stdClass;
		$result->count = $num;
		return $result;
	}
	

}