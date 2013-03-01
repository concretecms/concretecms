<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Model_AggregatorItem extends Object {

	abstract public function loadDetails();

	public function getAggregatorItemID() {return $this->agiID;}
	public function getAggregatorDataSourceHandle() {return $this->agsHandle;}
	public function getAggregatorItemPublicDateTime() {return $this->agiPublicDateTime;}

	public static function getByID($agiID) {
		$db = Loader::db();
		$r = $db->GetRow('select AggregatorItems.*, AggregatorDataSources.agsHandle from AggregatorItems inner join AggregatorDataSources on AggregatorItems.agsID = AggregatorDataSources.agsID where agiID = ?', array($agiID));
		if (is_array($r) && $r['agiID'] == $agiID) {
			$class = Loader::helper('text')->camelcase($r['agsHandle']) . 'AggregatorItem';
			$ags = new $class();
			$ags->setPropertiesFromArray($r);
			$ags->loadDetails();
			return $ags;
		}
	}

	public static function add(Aggregator $ag, AggregatorDataSource $ags, $agiPublicDateTime, $agiTitle) {
		$db = Loader::db();
		$agiDateTimeCreated = Loader::helper('date')->getSystemDateTime();
		$r = $db->Execute('insert into AggregatorItems (agID, agsID, agiDateTimeCreated, agiPublicDateTime, agiTitle) values (?, ?, ?, ?, ?)', array(
			$ag->getAggregatorID(),
			$ags->getAggregatorDataSourceID(), 
			$agiDateTimeCreated,
			$agiPublicDateTime, 
			$agiTitle
		));
		return AggregatorItem::getByID($db->Insert_ID());
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from AggregatorItems where agiID = ?', array($this->agiID));
	}

	public function render() {
		$dataSource = $this->getAggregatorDataSourceHandle();
		$env = Environment::get();
		// we can't just use Loader::element because it strips off .php of the filename. Lame.
		$path = $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_AGGREGATOR . '/' . DIRNAME_AGGREGATOR_GRID_TILES . '/' . $dataSource . '/' . FILENAME_AGGREGATOR_VIEW);
		$item = $this; // we have to define this so $item is defined in the include.
		include($path);
	}

	public function getAggregatorItemSizeX() {
		return 1;
	}
	public function getAggregatorItemSizeY() {
		return 1;
	}
}
