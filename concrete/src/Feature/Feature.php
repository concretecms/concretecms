<?php
namespace Concrete\Core\Feature;
use \Concrete\Core\Foundation\Object;
use Loader;
use \Concrete\Core\Package\PackageList;
use Core;
class Feature extends Object {

	public function getFeatureDetailObject($mixed) {
		if ($this->feHasCustomClass) {
			$class = '\\Concrete\\Core\\Feature\\Detail\\' . Loader::helper('text')->camelcase($this->feHandle) . 'Detail';
		} else {
			$class = '\\Concrete\\Core\\Feature\\Detail\\Detail';
		}
		$o = Core::make($class, array($mixed));
		return $o;
	}

	public static function getByID($feID) {
		$db = Loader::db();
		$row = $db->GetRow('select feID, feScore, feHasCustomClass, feHandle, pkgID from Features where feID = ?', array($feID));
		if (isset($row['feID'])) {
			$class = 'Feature';
			if ($row['feHasCustomClass']) {
				$class = Loader::helper('text')->camelcase($row['feHandle']) . $class;
			}
			$class = '\\Concrete\\Core\\Feature\\' . $class;
			$fe = Core::make($class);
			$fe->setPropertiesFromArray($row);
			return $fe;
		}
	}

	public static function getByHandle($feHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select feID, feScore, feHandle, feHasCustomClass, pkgID from Features where feHandle = ?', array($feHandle));
		if (isset($row['feID'])) {
			$class = 'Feature';
			if ($row['feHasCustomClass']) {
				$class = Loader::helper('text')->camelcase($row['feHandle']) . $class;
			}
			$class = '\\Concrete\\Core\\Feature\\' . $class;
			$fe = Core::make($class);
			$fe->setPropertiesFromArray($row);
			return $fe;
		}
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select feID from Features where pkgID = ? order by feID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$fe = static::getByID($row['feID']);
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
			$fe = static::getByID($row['feID']);
			if (is_object($fe)) {
				$list[] = $fe;
			}
		}
		$r->Close();
		return $list;
	}

	public function getFeatureID() {return $this->feID;}
	public function getFeatureHandle() {return $this->feHandle;}
	public function getFeatureName() {return Loader::helper('text')->unhandle($this->feHandle);}
	public function getFeatureScore() {return $this->feScore;}
	public function hasFeatureCustomClass() {return $this->feHasCustomClass;}
	public function getPackageID() {return $this->pkgID;}
	public function getPackageHandle() {return PackageList::getHandle($this->pkgID);}

	public static function add($feHandle, $feScore = 1, $feHasCustomClass = false, $pkg = false) {
		$db = Loader::db();
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		if (!$feScore) {
			$feScore = 1;
		}
		if ($feHasCustomClass) {
			$feHasCustomClass = 1;
		} else {
			$feHasCustomClass = 0;
		}

		$db->Execute('insert into Features (feHandle, feScore, feHasCustomClass, pkgID) values (?, ?, ?, ?)', array($feHandle, $feScore, $feHasCustomClass, $pkgID));
		$id = $db->Insert_ID();

		$fe = static::getByID($id);
		return $fe;
	}


	public function export($fxml, $full = true) {
		$fe = $fxml->addChild('feature');
		$fe->addAttribute('handle',$this->getFeatureHandle());
		if ($full) {
			$fe->addAttribute('score',$this->getFeatureScore());
			$fe->addAttribute('has-custom-class', $this->hasFeatureCustomClass());
			$fe->addAttribute('package', $this->getPackageHandle());
		}
		return $fe;
	}

	public static function exportList($xml) {
		$fxml = $xml->addChild('features');
		$db = Loader::db();
		$r = $db->Execute('select feID from Features order by feID asc');
		$list = array();
		while ($row = $r->FetchRow()) {
			$fe = static::getByID($row['feID']);
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
