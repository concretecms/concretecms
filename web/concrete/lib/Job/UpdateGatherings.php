<?
namespace Concrete\Job;
use Job;
class UpdateGatherings extends Job {

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
