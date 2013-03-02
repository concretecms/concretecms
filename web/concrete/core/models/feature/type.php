<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Model_FeatureType extends Object {

	abstract public function getFeatureDetailObject($mixed);

	public static function getByID($ftID) {
		$db = Loader::db();
		$row = $db->GetRow('select ftID, ftHandle, pkgID from FeatureTypes where ftID = ?', array($ftID));
		if (isset($row['ftID'])) {
			$class = Loader::helper('text')->camelcase($row['ftHandle']) . 'FeatureType';
			$fe = new $class();
			$fe->setPropertiesFromArray($row);
			return $fe;
		}
	}
	
	public static function getByHandle($ftHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select ftID, ftHandle, pkgID from FeatureTypes where ftHandle = ?', array($ftHandle));
		if (isset($row['ftID'])) {
			$class = Loader::helper('text')->camelcase($row['ftHandle']) . 'FeatureType';
			$fe = new $class();
			$fe->setPropertiesFromArray($row);
			return $fe;
		}
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select ftID from FeatureTypes where pkgID = ? order by ftID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$fe = FeatureType::getByID($row['ftID']);
			if (is_object($fe)) {
				$list[] = $fe;
			}
		}
		$r->Close();
		return $list;
	}	

	public static function getList() {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select ftID from FeatureTypes order by ftID asc');
		while ($row = $r->FetchRow()) {
			$fe = FeatureType::getByID($row['ftID']);
			if (is_object($fe)) {
				$list[] = $fe;
			}
		}
		$r->Close();
		return $list;
	}	
	
	public function getFeatureTypeID() {return $this->ftID;}
	public function getFeatureTypeHandle() {return $this->ftHandle;}
	public function getPackageID() {return $this->pkgID;}
	public function getPackageHandle() {return PackageList::getHandle($this->pkgID);}

	public static function add($ftHandle, $pkg = false) {
		$db = Loader::db();
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
	
		$db->Execute('insert into FeatureTypes (ftHandle, pkgID) values (?, ?)', array($ftHandle, $pkgID));
		$id = $db->Insert_ID();
		
		$fe = FeatureType::getByID($id);
		return $fe;
	}


	public function export($fxml) {
		$fe = $fxml->addChild('featuretype');
		$fe->addAttribute('handle',$this->getFeatureTypeHandle());
		$fe->addAttribute('package', $this->getPackageHandle());
		return $fe;
	}

	public static function exportList($xml) {
		$fxml = $xml->addChild('featuretypes');
		$db = Loader::db();
		$r = $db->Execute('select ftID from FeatureTypes order by ftID asc');
		$list = array();
		while ($row = $r->FetchRow()) {
			$fe = FeatureType::getByID($row['ftID']);
			if (is_object($fe)) {
				$list[] = $fe;
			}
		}
		foreach($list as $fe) {
			$fe->export($fxml);
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from FeatureTypes where ftID = ?', array($this->ftID));
	}
	
		
}
