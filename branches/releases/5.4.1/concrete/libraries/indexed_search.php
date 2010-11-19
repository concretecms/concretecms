<?php 
/**
*
* A wrapper class for results from the search engine, allowing for abstraction in case search engines are changed in the future.
* @package Utilities
* @subpackage Search
*/
defined('C5_EXECUTE') or die("Access Denied.");

class IndexedSearchResult {


	public function __construct($id, $name, $description, $score, $cPath, $cBody='') {
		$this->cID = $id;
		$this->cName = $name;
		$this->cDescription = $description;		
		$this->score = $score;
		$this->cPath = $cPath;
		$this->cBody = $cBody;
	}

	public function getID() {return $this->cID;}
	public function getName() {return $this->cName;}
	public function getDescription() {return $this->cDescription;}
	public function getScore() {return $this->score;}
	public function getCPath() {return $this->cPath;}
	public function getCBody() {return $this->cBody;}
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
		return $text;
	}
	
	/** 
	 * Reindexes the search engine.
	 */
	public function reindex() {

		Loader::library('3rdparty/Zend/Search/Lucene');
		Loader::library('3rdparty/StandardAnalyzer/Analyzer/Standard/English');

		$index = new Zend_Search_Lucene(DIR_FILES_CACHE_PAGES, true);
		//Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new StandardAnalyzer_Analyzer_Standard_English());
		
		$db = Loader::db();
		$collection_attributes = Loader::model('collection_attributes');
		$r = $db->query("select cID from Pages order by cID asc");
		$g = Group::getByID(GUEST_GROUP_ID);
		$nh = Loader::helper('navigation');
		
		while ($row = $r->fetchRow()) {
			$c = Page::getByID($row['cID'], 'ACTIVE');
			
			if($c->getCollectionAttributeValue('exclude_search_index')) continue;		
			
			$themeObject = $c->getCollectionThemeObject();
			$g->setPermissionsForObject($c);
			if ($g->canRead()) {			
				$pageID = md5($row['cID']);
				$doc = new Zend_Search_Lucene_Document();
				$doc->addField(Zend_Search_Lucene_Field::Keyword('cIDhash', $pageID));
				$doc->addField(Zend_Search_Lucene_Field::Unindexed('cID', $row['cID']));
				$doc->addField(Zend_Search_Lucene_Field::Text('cName', $c->getCollectionName(), APP_CHARSET));
				$doc->addField(Zend_Search_Lucene_Field::Keyword('ctHandle', $c->getCollectionTypeHandle()));
				$doc->addField(Zend_Search_Lucene_Field::Text('cDescription', $c->getCollectionDescription(), APP_CHARSET));
				$doc->addField(Zend_Search_Lucene_Field::Text('cBody', $this->getBodyContentFromPage($c), APP_CHARSET));
				
				if (is_object($themeObject)) {
					$doc->addField(Zend_Search_Lucene_Field::Text('cTheme', $themeObject->getThemeHandle()));
				}
				$doc->addField(Zend_Search_Lucene_Field::Text( 'cPath', $c->getCollectionPath())); 
				
				if (count($this->cPathSections) > 0) {
					foreach($this->cPathSections as $var => $cPath) {
						$isInSection = (strstr(strtolower($c->getCollectionPath()), $cPath . '/')) ? 'true' : 'false';
						$doc->addField(Zend_Search_Lucene_Field::Keyword($var, $isInSection));
					}
				}
				
				$attributes=$c->getSetCollectionAttributes();
				foreach($attributes as $attribute){
					if ($attribute->isCollectionAttributeKeySearchable()) {
						$doc->addField(Zend_Search_Lucene_Field::Keyword( $attribute->akHandle, $c->getCollectionAttributeValue($attribute) ));
					}
				}
				
				$index->addDocument($doc);
			}			
		}
		$result = new stdClass;
		$result->count = $index->count();
		return $result;
	}
	
	public function setCollectionPathSection($path, $sectionVar) {
		$this->cPathSections[$sectionVar] = $path;
	}
	
	
	//Required Subquery Multi-dimensional Array Structure
	//$subqueries = array( array( 'query'=>$query1,'required'=>true ), array('query'=>$query1,'required'=>NULL) ) )
	public static function search( $query, $subqueries = array()) {
		
		$query = strtolower($query);
		
		Loader::library('3rdparty/Zend/Search/Lucene');
		Loader::library('3rdparty/StandardAnalyzer/Analyzer/Standard/English');
		
		$index = new Zend_Search_Lucene(DIR_FILES_CACHE_PAGES);
		$index->setResultSetLimit(200);
		
		//Zend_Search_Lucene_Analysis_Analyzer::setDefault(new StandardAnalyzer_Analyzer_Standard_English());
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new StandardAnalyzer_Analyzer_Standard_English());
		
		$queryModifiers=array();

		$mainQuery = Zend_Search_Lucene_Search_QueryParser::parse($query, APP_CHARSET);

		$query = new Zend_Search_Lucene_Search_Query_Boolean();
		$query->addSubquery($mainQuery, true);
		
		foreach($subqueries as $subQ) {
			if( !is_array($subQ) || !isset( $subQ['query'] ) )
				 $subQuery = $subQ;				 
			else $subQuery = $subQ['query']; 			
						
			if( !is_array($subQ) || !isset($subQ['required']) )
				 $required=true;
			else $required=$subQ['required'];
			
			$query->addSubquery( $subQuery, $required );	
		}
		$query = utf8_encode($query);
		$resultsTmp = $index->find($query);

		$results = array();
		foreach($resultsTmp as $r)
			$results[] = new IndexedSearchResult($r->cID, $r->cName, $r->cDescription, $r->score, $r->cPath, $r->cBody);
		
		return $results;
	}


}