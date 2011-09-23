<?

defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('page_list');
class StackList extends PageList {

	public function __construct() {
		$c = Page::getByPath(STACKS_PAGE_PATH);
		$this->ignoreAliases = true;
		$this->filterByParentID($c->getCollectionID());
	}
	
}
