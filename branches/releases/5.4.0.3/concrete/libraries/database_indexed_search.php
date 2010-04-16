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
	public function getDate($mask = '') {
		if ($mask == '') {
			$mask = t('Y-m-d H:i:s');
		}
		return date($mask, strtotime($this->cDate));
	}
	public function setDate($date) { $this->cDate = $date;}
}

/** 
 * @DEPRECATED.
 * Just use PageList with filterByKeywords instead. We'll keep this around so people know what to expect
 */
class IndexedPageList extends PageList {

	public function getPage() {
		$this->sortByMultiple('cIndexScore desc', 'cDatePublic desc');
		$r = parent::getPage();
		$results = array();
		foreach($r as $c) {
			$results[] = array('cID' => $c->getCollectionID(), 'cName' => $c->getCollectionName(), 'cDescription' => $c->getCollectionDescription(), 'score' => $c->getPageIndexScore(), 'cPath' => $c->getCollectionPath(), 'content' => $c->getPageIndexContent());
		}
		return $results;
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
	private $searchableAreaNamesManual = array();
	
	public function addSearchableArea($arr) {
		$this->searchableAreaNamesManual[] = $arr;
	}
	
	public function getSearchableAreaAction() {
		$action = Config::get('SEARCH_INDEX_AREA_METHOD');
		if (!$action) {
			$action = 'whitelist';
		}
		return $action;
	}
	
	public function getSavedSearchableAreas() {
		$areas = Config::get('SEARCH_INDEX_AREA_LIST');
		$areas = unserialize($areas);
		if (!is_array($areas)) {
			$areas = array();
		}
		return $areas;
	}
	
	public function getBodyContentFromPage($c) {
		$searchableAreaNamesInitial=$this->getSavedSearchableAreas();
		foreach($this->searchableAreaNamesManual as $sm) {
			$searchableAreaNamesInitial[] = $sm;
		}
		
		$searchableAreaNames = array();
		if ($this->getSearchableAreaAction() == 'blacklist') {
			$areas = Area::getHandleList();
			foreach($areas as $arHandle) {
				if (!in_array($arHandle, $searchableAreaNamesInitial)) {
					$searchableAreaNames[] = $arHandle;
				}
			}
		} else {
			$searchableAreaNames = $searchableAreaNamesInitial;
		}		

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
			
			$c->reindex($this);
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