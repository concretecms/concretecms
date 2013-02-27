<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_Aggregator extends Object {

	public function getAggregatorID() {return $this->agID;}
	
	public static function getByID($agID) {
		$db = Loader::db();
		$r = $db->GetRow('select agID, agDateCreated from Aggregators where agID = ?', array($agID));
		if (is_array($r) && $r['agID'] == $agID) {
			$ag = new Aggregator;
			$ag->setPropertiesFromArray($r);
			return $ag;
		}
	}

	public static function add() {
		$db = Loader::db();
		$date = Loader::helper('date')->getSystemDateTime();
		$r = $db->Execute('insert into Aggregators (agDateCreated) values (?)', array($date));
		return Aggregator::getByID($db->Insert_ID());
	}

	public function getConfiguredAggregatorDataSources() {
		$db = Loader::db();
		$r = $db->Execute('select acsID from AggregatorConfiguredDataSources where agID = ?', array($this->agID));
		$list = array();
		while ($row = $r->FetchRow()) {
			$source = AggregatorDataSourceConfiguration::getByID($row['acsID']);
			if (is_object($source)) {
				$list[] = $source;
			}
		}
		return $list;
	}

	public function clearConfiguredAggregatorDataSources() {
		$sources = $this->getConfiguredAggregatorDataSources();
		foreach($sources as $s) {
			$s->delete();
		}
	}

	/** 
	 * Runs through all active aggregator data sources, creates AggregatorItem objects
	 */
	public function generateAggregatorItems() {
		$configuredDataSources = $this->getConfiguredAggregatorDataSources();
		foreach($configuredDataSources as $configuration) {
			$dataSource = $configuration->getAggregatorDataSourceObject();
			$items = $dataSource->createAggregatorItems($configuration);
		}
	}


}
