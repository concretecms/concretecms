<?
namespace Concrete\Core\Page\Search;
use Loader;
use PageList;
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
			$results[] = array('cID' => $c->getCollectionID(), 'cName' => $c->getCollectionName(), 'cDescription' => $c->getCollectionDescription(), 'score' => $c->getPageIndexScore(), 'cPath' => $c->getCollectionPath(), 'content' => $c->getPageIndexContent(), 'cDatePublic' => $c->getCollectionDatePublic());
		}
		return $results;
	}
}