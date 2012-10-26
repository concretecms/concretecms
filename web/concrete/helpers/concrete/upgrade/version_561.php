<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion561Helper {
	
	public $dbRefreshTables = array(
		'atSocialLinks',
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

		$js = JobSet::getByHandle('Default');
		if (!is_object($js)) {
			$js = JobSet::add('Default');
		}
		$js->clearJobs();
		$jobs = Job::getList();
		foreach($jobs as $j) {
			if (!$j->supportsQueue()) {
				$j->addJob($j);	
			}
		}

		$j = Job::getByHandle('index_search_all');
		if (!is_object($j)) {
			Job::installByHandle('index_search_all');
		}

	}
		
}
