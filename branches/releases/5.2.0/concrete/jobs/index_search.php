<?php 
/**
*
* Responsible for loading the indexed search class and initiating the reindex command.
* @package Utilities
*/

defined('C5_EXECUTE') or die(_("Access Denied."));
class IndexSearch extends Job {

	public $jNotUninstallable=1;
	
	function getJobName() {
		return t("Index Search Engine");
	}
	
	function getJobDescription() {
		return t("Index the site to allow searching to work quickly and accurately.");
	}
	
	function run() {
		Loader::library('indexed_search');
		$is = new IndexedSearch();
		$result = $is->reindex();
		return t('%s page(s) indexed.', $result->count);
	}

}

?>