<?php 
/**
*
* Responsible for loading the indexed search class and initiating the reindex command.
* @package Utilities
*/

defined('C5_EXECUTE') or die(_("Access Denied."));
class IndexSearch extends Job {

	public $jNotUninstallable=1;
	
	public function getJobName() {
		return t("Index Search Engine");
	}
	
	public function getJobDescription() {
		return t("Index the site to allow searching to work quickly and accurately.");
	}
	
	public function run() {
		Loader::model('attribute/categories/collection');
		Loader::model('attribute/categories/file');
		Loader::model('attribute/categories/user');
		Cache::disableLocalCache();
		$attributes = CollectionAttributeKey::getList();
		$attributes = array_merge($attributes, FileAttributeKey::getList());
		$attributes = array_merge($attributes, UserAttributeKey::getList());
		foreach($attributes as $ak) {
			$ak->updateSearchIndex();
		}

		Loader::library('database_indexed_search');
		$is = new IndexedSearch();
		$result = $is->reindexAll();
		return t('%s page(s) indexed.', $result->count);
	}

}

?>