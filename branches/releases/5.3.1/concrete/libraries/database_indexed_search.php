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
		$db = Loader::db();
		$kw = $db->quote($kw);
		$this->addToQuery("select PageSearchIndex.*, match(cName, cDescription, content) against ({$kw} in boolean mode) as score from PageSearchIndex");
		$this->filter(false, "match(cName, cDescription, content) against ({$kw} in boolean mode)");
	}
	
	private $searchPaths = array();
	
	public function addSearchPath($path) {
		$this->searchPaths[] = $path;
	}
	
	public function getPage() {
		$db = Loader::db(); 
		
		if (count($this->searchPaths) > 0) { 
			$i = 0;
			$subfilter = '';
			foreach($this->searchPaths as $sp) {
				$sp = $db->quote($sp . '%');
				$subfilter .= "cPath like {$sp} ";
				if (($i+1) < count($this->searchPaths)) {
					$subfilter .= "or ";
				}
				$i++;
			}
			$this->filter(false, $subfilter);
		}

		$this->sortByMultiple('score desc', 'cDatePublic desc');
		return parent::getPage();
	}
}

/**
*
* A wrapper class for the search engine that Concrete integrates (currently Lucene as implemented by the Zend Framework.)
* @package Utilities
* @subpackage Search
*/
class IndexedSearch {
	
	private $cPathSections = array();
	private $searchableAreaNames = array('Main Content', 'Main');
	
	public function addSearchableArea($arr) {
		$this->searchableAreaNames[] = $arr;
	}
	
	private function getBodyContentFromPage($c) {
		$searchableAreaNames=$this->searchableAreaNames;
		$blarray=array();
		foreach($searchableAreaNames as $searchableAreaName){
		 	$blarray = array_merge( $blarray, $c->getBlocks($searchableAreaName) );
		}
		$text = '';
		foreach($blarray as $b) {
			if ($b->getBlockTypeHandle() == 'content') {
				$bi = $b->getInstance();
				$text .= strip_tags($bi->content);
			}
		}
		unset($blarray);
		return $text;
	}
	
	/** 
	 * Reindexes the search engine.
	 */
	public function reindex() {
		Cache::disableLocalCache();

		$db = Loader::db();
		$collection_attributes = Loader::model('collection_attributes');
		$r = $db->query("select cID from Pages order by cID asc");
		$g = Group::getByID(GUEST_GROUP_ID);
		$nh = Loader::helper('navigation');
		
		$db->Execute("truncate table PageSearchIndex");
		$db->Execute("truncate table PageSearchIndexAttributes");
		
		$num = 0;
		while ($row = $r->fetchRow()) {
			$c = Page::getByID($row['cID'], 'ACTIVE');
			$themeObject = $c->getCollectionThemeObject();
			$g->setPermissionsForObject($c);
			if ($g->canRead()) {
				$v = array($row['cID'], $c->getCollectionName(), $c->getCollectionDescription(), $c->getCollectionPath(), $c->getCollectionDatePublic(), $this->getBodyContentFromPage($c));
				$db->Execute("insert into PageSearchIndex (cID, cName, cDescription, cPath, cDatePublic, content) values (?, ?, ?, ?, ?, ?)", $v);
				unset($v);

				$attributes=$c->getSetCollectionAttributes();
				foreach($attributes as $attribute){
					if ($attribute->isCollectionAttributeKeySearchable()) {
						$val = $db->GetOne("select value from CollectionAttributeValues where cID = ? and cvID = ? and akID = ?", array($c->getCollectionID(), $c->getVersionID(), $attribute->getCollectionAttributeKeyID()));
						$v = array($row['cID'], $attribute->getAttributeKeyID(), $attribute->getAttributeKeyHandle(), $val);
						$db->Execute("insert into PageSearchIndexAttributes (cID, akID, akHandle, value) values (?, ?, ?, ?)", $v);
					}
				}
				unset($v);
				$num++;
			}
			
			unset($c);
		}
		
		$r->Close();
		Cache::enableLocalCache();
		$result = new stdClass;
		$result->count = $num;
		return $result;
	}
	

}