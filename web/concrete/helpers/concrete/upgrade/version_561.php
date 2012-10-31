<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion561Helper {
	
	public $dbRefreshTables = array(
		'atSocialLinks',
		'UserPointActions',
		'UserPointHistory',
		'Groups',
		'QueuePageDuplicationRelations',
		'Queues',
		'QueueMessages',
		'JobSets',
		'JobSetJobs'
	);
	
	
	public function run() {
		$tt = AttributeType::getByHandle('social_links');
		if (!is_object($tt) || $tt->getAttributeTypeID() == 0) {
			$tt = AttributeType::add('social_links', t('Social Link'));
		}
		$akc = AttributeKeyCategory::getByHandle('user');
		if (is_object($akc)) {
			$akc->associateAttributeKeyType($tt);
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

		$j = Job::getByHandle('index_search_all');
		if (!is_object($j)) {
			Job::installByHandle('index_search_all');
		}

		$j = Job::getByHandle('check_automated_groups');
		if (!is_object($j)) {
			Job::installByHandle('check_automated_groups');
		}

		$sp = Page::getByPath('/dashboard/users/points');
		if ($sp->isError()) {
			$sp = SinglePage::add('/dashboard/users/points');
			$sp->update(array('cName'=>t('Community Points')));
			$sp->setAttribute('icon_dashboard', 'icon-heart');
		}

		$sp = Page::getByPath('/dashboard/users/points/assign');
		if ($sp->isError()) {
			$sp = SinglePage::add('/dashboard/users/points/assign');
			$sp->update(array('cName'=>t('Assign Points')));
		}

		$sp = Page::getByPath('/dashboard/users/points/actions');
		if ($sp->isError()) {
			$sp = SinglePage::add('/dashboard/users/points/actions');
		}

	}
		
}
