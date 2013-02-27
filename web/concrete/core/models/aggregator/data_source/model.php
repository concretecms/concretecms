<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Model_AggregatorDataSource extends Object {

	abstract public function createConfigurationObject(Aggregator $ag, $post);
	abstract public function getAggregatorItems(AggregatorDataSourceConfiguration $configuration);
	
	public function configure(Aggregator $ag, $post) {
		$db = Loader::db();
		$o = $this->createConfigurationObject($ag, $post);
		$r = $db->Execute('insert into AggregatorConfiguredDataSources (agID, agsID, acdObject) values (?, ?, ?)', array(
			$ag->getAggregatorID(), $this->agsID, serialize($o)
		));
		return AggregatorDataSourceConfiguration::getByID($db->Insert_ID());
	}

	public static function getByID($agsID) {
		$db = Loader::db();
		$row = $db->GetRow('select agsID, agsHandle, pkgID, agsName from AggregatorDataSources where agsID = ?', array($agsID));
		if (isset($row['agsID'])) {
			$class = Loader::helper('text')->camelcase($row['agsHandle']) . 'AggregatorDataSource';
			$ags = new $class();
			$ags->setPropertiesFromArray($row);
			return $ags;
		}
	}
	
	public static function getByHandle($agsHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select agsID, agsHandle, pkgID, agsName from AggregatorDataSources where agsHandle = ?', array($agsHandle));
		if (isset($row['agsID'])) {
			$class = Loader::helper('text')->camelcase($row['agsHandle']) . 'AggregatorDataSource';
			$ags = new $class();
			$ags->setPropertiesFromArray($row);
			return $ags;
		}
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select agsID from AggregatorDataSources where pkgID = ? order by agsID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$ags = AggregatorDataSource::getByID($row['agsID']);
			if (is_object($ags)) {
				$list[] = $ags;
			}
		}
		$r->Close();
		return $list;
	}	

	public static function getList() {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select agsID from AggregatorDataSources order by agsDisplayOrder asc');
		while ($row = $r->FetchRow()) {
			$ags = AggregatorDataSource::getByID($row['agsID']);
			if (is_object($ags)) {
				$list[] = $ags;
			}
		}
		$r->Close();
		return $list;
	}	
	
	public function getAggregatorDataSourceID() {return $this->agsID;}
	public function getAggregatorDataSourceHandle() {return $this->agsHandle;}
	public function getAggregatorDataSourceName() {return $this->agsName;}
	public function getPackageID() {return $this->pkgID;}
	public function getPackageHandle() {return PackageList::getHandle($this->pkgID);}
	public function getAggregatorDataSourceOptionsForm() {
		$env = Environment::get();
		$file = $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_AGGREGATOR . '/' . DIRNAME_AGGREGATOR_DATA_SOURCES . '/' . $this->agsHandle . '/' . FILENAME_AGGREGATOR_DATA_SOURCE_OPTIONS, $this->getPackageHandle());
		return $file;
	}
	

	public function updateAggregatorDataSourceName($agsName) {
		$this->agsName = $agsName;
		$db = Loader::db();
		$db->Execute("update AggregatorDataSources set agsName = ? where agsID = ?", array($agsName, $this->agsID));
	}

	public function updateAggregatorDataSourceHandle($agsHandle) {
		$this->agsHandle = $agsHandle;
		$db = Loader::db();
		$db->Execute("update AggregatorDataSources set agsHandle = ? where agsID = ?", array($agsHandle, $this->agsID));
	}
	
	public static function add($agsHandle, $agsName, $pkg = false) {
		$db = Loader::db();
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$sources = $db->GetOne('select count(agsID) from AggregatorDataSources');
		$agsDisplayOrder = 0;
		if ($sources > 0) {
			$agsDisplayOrder = $db->GetOne('select max(agsDisplayOrder) from AggregatorDataSources');
			$agsDisplayOrder++;
		}
		
		$db->Execute('insert into AggregatorDataSources (agsHandle, agsName, pkgID) values (?, ?, ?)', array($agsHandle, $agsName, $pkgID));
		$id = $db->Insert_ID();
		
		$ags = AggregatorDataSource::getByID($id);
		return $ags;
	}


	public function export($axml) {
		$ags = $axml->addChild('aggregatorsource');
		$ags->addAttribute('handle',$this->getAggregatorDataSourceHandle());
		$ags->addAttribute('name', $this->getAggregatorDataSourceName());
		$ags->addAttribute('package', $this->getPackageHandle());
		return $ags;
	}

	public static function exportList($xml) {
		$axml = $xml->addChild('aggregatorsources');
		$db = Loader::db();
		$r = $db->Execute('select agsID from AggregatorDataSources order by agsID asc');
		$list = array();
		while ($row = $r->FetchRow()) {
			$ags = AggregatorDataSource::getByID($row['agsID']);
			if (is_object($ags)) {
				$list[] = $ags;
			}
		}
		foreach($list as $ags) {
			$ags->export($axml);
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from AggregatorDataSources where agsID = ?', array($this->agsID));
	}
	
		
}
