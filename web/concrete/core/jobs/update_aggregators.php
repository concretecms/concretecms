<?
/**
*
* Responsible for loading the indexed search class and initiating the reindex command.
* @package Utilities
*/

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Job_UpdateGatherings extends Job {

	public function getJobName() {
		return t("Update Gatherings");
	}
	
	public function getJobDescription() {
		return t("Loads new items into gatherings.");
	}

	public function run() {
		// retrieve all gatherings
		$list = Gathering::getList();
		foreach($list as $gathering) {
			// generate all new items since the last time the gathering was updated.
			$gathering->generateGatheringItems();
		}
	}
}
