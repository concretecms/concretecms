<?
namespace Concrete\Core\Page\Workflow\Progress;
use \Concrete\Core\Page\Page\List as PageList;
class List extends PageList {
	
	protected $autoSortColumns = array('wpDateLastAction', 'cvName', 'wpCurrentStatus');
	
	public function __construct() {
		$this->includeInactivePages();
		$this->includeSystemPages();
		$this->displayUnapprovedPages();
		$this->ignoreAliases();
		parent::setBaseQuery(', pwp.wpID, wp.wpCurrentStatus');
		$this->addToQuery('inner join PageWorkflowProgress pwp on p1.cID = pwp.cID inner join WorkflowProgress wp on wp.wpID = pwp.wpID');
		$this->filter('wpIsCompleted', 0);
	}

	public function get($itemsToGet = 0, $offset = 0) {
		$_pages = DatabaseItemList::get($itemsToGet, $offset);
		$pages = array();
		foreach($_pages as $row) {
			$c = Page::getByID($row['cID']);
			$cp = new Permissions($c);
			if ($cp->canViewPageVersions()) { 
				$c->loadVersionObject('RECENT');
			} else {
				$c->loadVersionObject('ACTIVE');
			}
			$wp = PageWorkflowProgress::getByID($row['wpID']);
			$pages[] = new PageWorkflowProgressPage($c, $wp);
		}
		return $pages;
	}
}