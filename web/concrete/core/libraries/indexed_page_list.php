<?
/**
*
* A wrapper class for results from the search engine, allowing for abstraction in case search engines are changed in the future.
* @package Utilities
* @subpackage Search
*/
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_IndexedPageList extends PageList {

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