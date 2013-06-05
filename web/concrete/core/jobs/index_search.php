<?
/**
*
* Responsible for loading the indexed search class and initiating the reindex command.
* @package Utilities
*/

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Job_IndexSearch extends Job {

	public $jNotUninstallable=1;
	
	public function getJobName() {
		return t("Index Search Engine - Updates");
	}
	
	public function getJobDescription() {
		return t("Index the site to allow searching to work quickly and accurately. Only reindexes pages that have changed since last indexing.");
	}
	
	public function run() {
		Cache::disableCache();

		Loader::library('database_indexed_search');
		$is = new IndexedSearch();
		if ($_GET['force'] == 1) {
			Loader::model('attribute/categories/collection');
			Loader::model('attribute/categories/file');
			Loader::model('attribute/categories/user');
			$attributes = CollectionAttributeKey::getList();
			$attributes = array_merge($attributes, FileAttributeKey::getList());
			$attributes = array_merge($attributes, UserAttributeKey::getList());
			foreach($attributes as $ak) {
				$ak->updateSearchIndex();
			}

			$result = $is->reindexAll(true);
		} else {
			$result = $is->reindexAll();
		}
		if ($result->count == 0) {
			return t('Indexing complete. Index is up to date');
		} else {
			if ($result->count == $is->searchBatchSize) {
				return t('Index partially updated. %s pages indexed (maximum number.) Re-run this job to continue this process.', $result->count);
			} else {
				return t('Index updated.').' '.t2('%d page required reindexing.', '%d pages required reindexing.', $result->count, $result->count);
			}
		}
	}

}

?>