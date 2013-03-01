<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AggregatorDataSourceConfiguration extends Object {

	protected $dataSource;

	public static function getByID($acsID) {
		$db = Loader::db();
		$row = $db->GetRow('select acsID, agsID, agID, acdObject from AggregatorConfiguredDataSources where acsID = ?', array($acsID));
		if (isset($row['acsID'])) {
			$source = AggregatorDataSource::getByID($row['agsID']);
			$o = @unserialize($row['acdObject']);
			if (is_object($o)) {
				unset($row['acdObject']);
				$o->setPropertiesFromArray($row);
				$o->dataSource = AggregatorDataSource::getByID($row['agsID']);
				return $o;
			}
		}
	}

	public function __call($method, $args) {
		return call_user_func_array(array($this->dataSource, $method), $args);
	}

	public function getAggregatorDataSourceObject() {
		return $this->dataSource;
	}

	public function getAggregatorObject() {
		$aggregator = Aggregator::getByID($this->agID);
		return $aggregator;
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from AggregatorConfiguredDataSources where acsID = ?', array($this->acsID));
	}
		
}
