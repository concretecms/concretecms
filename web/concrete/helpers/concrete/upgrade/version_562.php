<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion562Helper {

	
	public $dbRefreshTables = array(
		'Queues',
		'QueueMessages',
		'QueuePageDuplicationRelations',
		'Jobs',
		'JobSets',
		'JobSetJobs',
		'Blocks',
		'Pages'
	);

	public function run() {
		$j = Job::getByHandle('index_search_all');
		if (!is_object($j)) {
			Job::installByHandle('index_search_all');
		}

		$js = JobSet::getByName('Default');
		if (!is_object($js)) {
			$js = JobSet::add('Default');
		}
		$js->clearJobs();
		$jobs = Job::getList();
		foreach($jobs as $j) {
			if (!$j->supportsQueue()) {
				$js->addJob($j);	
			}
		}

		// create the view page in sitemap permission
		$rpk = PermissionKey::getByHandle('view_page');
		$vpk = PermissionKey::getByHandle('view_page_in_sitemap');
		if (!is_object($vpk)) {
			$vpk = PermissionKey::add('page', 'view_page_in_sitemap', 'View Page in Sitemap', 'Controls whether a user can see a page in the sitemap or intelligent search.', false, false);
		}
		// now we have to get a list of all pages in the site that have their own permissions set.
		$db = Loader::db();
		$r = $db->Execute('select cID from Pages where cInheritPermissionsFrom = "OVERRIDE" order by cID asc');
		while ($row = $r->Fetchrow()) {
			$c = Page::getByID($row['cID']);
			if (is_object($c) && !$c->isError()) {
				$rpk->setPermissionObject($c);
				$vpk->setPermissionObject($c);
				$rpa = $rpk->getPermissionAccessObject();
				if (is_object($rpa)) {
					$pt = $vpk->getPermissionAssignmentObject();
					if (is_object($pt)) {
						$pt->clearPermissionAssignment();
						$pt->assignPermissionAccess($rpa);						
					}
				}
			}
		}
	}


}
