<?php 
/**
*
* Responsible for loading the indexed search class and initiating the reindex command.
* @package Utilities
*/
class IndexSearch extends Job {

	public $jName="Index Search Engine";
	public $jDescription="Index the site to allow searching to work quickly and accurately.";
	public $jNotUninstallable=1;
	

	function run() {
		Loader::library('indexed_search');
		$is = new IndexedSearch();
		$result = $is->reindex();
		return $result->count . ' indexed.';
	}

}

?>