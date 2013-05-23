<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AggregatorPermissionResponse extends PermissionResponse {
	
	public function canEditAggregatorItems() {
		// Eventually this will be overrideable at a particular aggregator level.
		$tp = PermissionKey::getByHandle('edit_aggregators');
		return $tp->can();
	}

}