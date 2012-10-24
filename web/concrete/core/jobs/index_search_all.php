<?
/**
*
* Responsible for loading the indexed search class and initiating the reindex command.
* @package Utilities
*/

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Job_IndexSearchAll extends Job {

	public $jNotUninstallable=1;
	public $jSupportsQueue = true;

	public function getJobName() {
		return t("Reindex All Pages");
	}
	
	public function getJobDescription() {
		return t("Empties the page search index and reindexes all pages.");
	}

	public function addToQueue(Zend_Queue $q) {
		$db = Loader::db();
		$db->Execute('truncate table PageSearchIndex');
		$r = $db->Execute('select Pages.cID from Pages left join CollectionSearchIndexAttributes csia on Pages.cID = csia.cID where (ak_exclude_search_index is null or ak_exclude_search_index = 0) and cIsActive = 1');
		while ($row = $r->FetchRow()) {
			$q->send($row['cID']);
		}
	}
	
	public function __construct() {
		Loader::library('database_indexed_search');
		$this->is = new IndexedSearch();
	}

	public function processQueueItem(Zend_Queue_Message $msg) {
		$c = Page::getByID($msg->body, 'ACTIVE');
		$cv = $c->getVersionObject();
		if(!$cv->cvIsApproved) { 
			continue;
		}
		$c->reindex($this->is, true);
	}


}