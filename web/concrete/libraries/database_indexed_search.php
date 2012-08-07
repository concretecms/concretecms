<?
/**
*
* A wrapper class for results from the search engine, allowing for abstraction in case search engines are changed in the future.
* @package Utilities
* @subpackage Search
*/
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('page_list');

class IndexedSearchResult {

	public function __construct($id, $name, $description, $score, $cPath, $content) {
		$this->cID = $id;
		$this->cName = $name;
		$this->cDescription = $description;		
		$this->score = $score;
		$this->cPath = $cPath;
		$this->content = $content;
		$this->nh = Loader::helper('navigation');
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
	public function getPath() {
		$c = Page::getByID($this->cID);
		return $this->nh->getLinkToCollection($c, true);
	}
	
	public function setDate($date) { $this->cDate = $date;}
}

/** 
 * @DEPRECATED.
 * Just use PageList with filterByKeywords instead. We'll keep this around so people know what to expect
 */
class IndexedPageList extends PageList {

	protected $indexModeSimple = false;
	
	public function setSimpleIndexMode($indexModeSimple) {
		$this->indexModeSimple = $indexModeSimple;
	}
	
	public function getPage() {
		if ($this->indexModeSimple) {
			$this->sortBy('cDatePublic', 'desc');
		} else {
			$this->sortByMultiple('cIndexScore desc', 'cDatePublic desc');
		}
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
	
	public $searchBatchSize = PAGE_SEARCH_INDEX_BATCH_SIZE;
	public $searchReindexTimeout = PAGE_SEARCH_INDEX_LIFETIME;

	private $cPathSections = array();
	private $searchableAreaNamesManual = array();
	
	public function addSearchableArea($arr) {
		$this->searchableAreaNamesManual[] = $arr;
	}
	
	public function getSearchableAreaAction() {
		$action = Config::get('SEARCH_INDEX_AREA_METHOD');
		if (!strlen($action)) {
			$action = 'blacklist';
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
	
	public function reindexPage($page) {
		$db = Loader::db();			
		if (is_object($page) && ($page instanceof Collection) && ($page->getAttribute('exclude_search_index') != 1)) {
			$datetime = Loader::helper('date')->getSystemDateTime();
			$db->Replace('PageSearchIndex', array(
				'cID' => $page->getCollectionID(), 
				'cName' => $page->getCollectionName(), 
				'cDescription' => $page->getCollectionDescription(), 
				'cPath' => $page->getCollectionPath(),
				'cDatePublic' => $page->getCollectionDatePublic(), 
				'content' => $this->getBodyContentFromPage($page),
				'cDateLastIndexed' => $datetime
			), array('cID'), true);			
		} else {
			$db->Execute('delete from PageSearchIndex where cID = ?', array($page->getCollectionID()));
		}
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
		
		if (count($searchableAreaNames) == 0) {
			return false;
		}
		
		$text = '';

		$tagsToSpaces=array('<br>','<br/>','<br />','<p>','</p>','</ p>','<div>','</div>','</ div>');
		$blarray=array();
		$db = Loader::db();
		$r = $db->Execute('select bID, arHandle from CollectionVersionBlocks where cID = ? and cvID = ?', array($c->getCollectionID(), $c->getVersionID()));
		while ($row = $r->FetchRow()) {
			if (in_array($row['arHandle'], $searchableAreaNames)) {
				$b = Block::getByID($row['bID'], $c, $row['arHandle']);
				if (!is_object($b)) {
					continue;
				}
				$bi = $b->getInstance();
				$bi->bActionCID = $c->getCollectionID();
				if(method_exists($bi,'getSearchableContent')){
					$searchableContent = $bi->getSearchableContent();  
					if(strlen(trim($searchableContent))) 					
						$text .= strip_tags(str_ireplace($tagsToSpaces,' ',$searchableContent)).' ';
				}
				unset($b);
				unset($bi);
			}		
		}
		
		return $text;
	}
	
	/** 
	 * Reindexes the search engine.
	 */
	public function reindexAll($fullReindex = false) {
		Cache::disableLocalCache();
		
		$db = Loader::db();
		Loader::model('collection_attributes');
		
		if ($fullReindex) {
			$db->Execute("truncate table PageSearchIndex");
		}
		
		$pl = new PageList();
		$pl->ignoreAliases();
		$pl->ignorePermissions();
		$pl->sortByCollectionIDAscending();
		$pl->filter(false, '(c.cDateModified > psi.cDateLastIndexed or UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(psi.cDateLastIndexed) > ' . $this->searchReindexTimeout . ' or psi.cID is null or psi.cDateLastIndexed is null)');
		$pl->filter(false, '(ak_exclude_search_index is null or ak_exclude_search_index = 0)');
		$pages = $pl->get($this->searchBatchSize);
		
		$num = 0;
		foreach($pages as $c) { 
			
			// make sure something is approved
			$cv = $c->getVersionObject();
			if(!$cv->cvIsApproved) { 
				continue;
			}		

			$c->reindex($this, true);
			$num++;		
			unset($c);
		}
		
		$pnum = Collection::reindexPendingPages();
		$num = $num + $pnum;
		
		Cache::enableLocalCache();
		$result = new stdClass;
		$result->count = $num;
		return $result;
	}
	

}