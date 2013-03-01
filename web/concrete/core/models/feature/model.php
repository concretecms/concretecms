<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Model_Feature extends Object {

	public static function getByID($feID) {
		$db = Loader::db();
		$row = $db->GetRow('select feID, feHandle, pkgID from Features where feID = ?', array($feID));
		if (isset($row['feID'])) {
			$class = Loader::helper('text')->camelcase($row['feHandle']) . 'Feature';
			$fe = new $class();
			$fe->setPropertiesFromArray($row);
			return $fe;
		}
	}
	
	public static function getByHandle($feHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select feID, feHandle, pkgID from Features where feHandle = ?', array($feHandle));
		if (isset($row['feID'])) {
			$class = Loader::helper('text')->camelcase($row['feHandle']) . 'Feature';
			$fe = new $class();
			$fe->setPropertiesFromArray($row);
			return $fe;
		}
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select feID from Features where pkgID = ? order by feID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$fe = Feature::getByID($row['feID']);
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
		$r = $db->Execute('select feID from Features order by feID asc');
		while ($row = $r->FetchRow()) {
			$fe = Feature::getByID($row['feID']);
			if (is_object($fe)) {
				$list[] = $fe;
			}
		}
		$r->Close();
		return $list;
	}	
	
	public function getFeatureID() {return $this->feID;}
	public function getFeatureHandle() {return $this->feHandle;}
	public function getPackageID() {return $this->pkgID;}
	public function getPackageHandle() {return PackageList::getHandle($this->pkgID);}

	public static function add($feHandle, $pkg = false) {
		$db = Loader::db();
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
	
		$db->Execute('insert into Features (feHandle, pkgID) values (?, ?)', array($feHandle, $pkgID));
		$id = $db->Insert_ID();
		
		$fe = Feature::getByID($id);
		return $fe;
	}


	public function export($fxml) {
		$fe = $fxml->addChild('feature');
		$fe->addAttribute('handle',$this->getFeatureHandle());
		$fe->addAttribute('package', $this->getPackageHandle());
		return $fe;
	}

	public static function exportList($xml) {
		$fxml = $xml->addChild('features');
		$db = Loader::db();
		$r = $db->Execute('select feID from Features order by feID asc');
		$list = array();
		while ($row = $r->FetchRow()) {
			$fe = Feature::getByID($row['feID']);
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
		$db->Execute('delete from Features where feID = ?', array($this->feID));
	}
	
		
}
