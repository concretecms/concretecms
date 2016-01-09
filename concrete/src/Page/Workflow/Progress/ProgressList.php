<?php
namespace Concrete\Core\Page\Workflow\Progress;
use \Concrete\Core\Legacy\PageList as PageList;
use Loader;
use \Concrete\Core\Workflow\Progress\PageProgress as PageWorkflowProgress;
use Permissions;
use \Concrete\Core\Page\Page as ConcretePage;
use \Concrete\Core\Legacy\DatabaseItemList;
class ProgressList extends PageList {

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
			$c = ConcretePage::getByID($row['cID']);
			$cp = new Permissions($c);
			if ($cp->canViewPageVersions()) {
				$c->loadVersionObject('RECENT');
			} else {
				$c->loadVersionObject('ACTIVE');
			}
			$wp = PageWorkflowProgress::getByID($row['wpID']);
			$pages[] = new Page($c, $wp);
		}
		return $pages;
	}
}
