<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion562Helper {

	
	public $dbRefreshTables = array(
		'Queues',
		'QueueMessages',
		'QueuePageDuplicationRelations'
	);

	public function run() {
		$j = Job::getByHandle('index_search_all');
		if (!is_object($j)) {
			Job::installByHandle('index_search_all');
		}
	}


}
