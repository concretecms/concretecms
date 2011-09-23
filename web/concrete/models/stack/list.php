<?

defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('page_list');
class StackList extends PageList {

	public function __construct() {
		$c = Page::getByPath('/dashboard/stacks/list');
		$this->ignoreAliases = true;
		$this->filterByParentID($c->getCollectionID());
	}
	
}
