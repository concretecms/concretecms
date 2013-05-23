<?
/**
*
* Responsible for loading the indexed search class and initiating the reindex command.
* @package Utilities
*/

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Job_UpdateAggregators extends Job {

	public function getJobName() {
		return t("Update Aggregators");
	}
	
	public function getJobDescription() {
		return t("Loads new items into aggregators.");
	}

	public function run() {
		// retrieve all aggregators
		$list = Aggregator::getList();
		foreach($list as $aggregator) {
			// generate all new items since the last time the aggregator was updated.
			$aggregator->generateAggregatorItems();
		}
	}
}