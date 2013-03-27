<?php defined('C5_EXECUTE') or die("Access Denied.");

abstract class Concrete5_Model_ComposerControlType extends Object {

	abstract public function getComposerControlObjects();
	
	public function getComposerControlTypeName() {return $this->cmpControlTypeName;}
	public function getComposerControlTypeHandle() {return $this->cmpControlTypeHandle;}
	public function getComposerControlTypeID() { return $this->cmpControlTypeID;}
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	public function getPackageObject() {return Package::getByID($this->pkgID);}

	public static function getByHandle($cmpControlTypeHandle) {
		$db = Loader::db();
		$r = $db->GetRow('select cmpControlTypeID, cmpControlTypeHandle, cmpControlTypeName, pkgID from ComposerControlTypes where cmpControlTypeHandle = ?', array($cmpControlTypeHandle));
		if (is_array($r) && $r['cmpControlTypeHandle']) {
			$class = Loader::helper('text')->camelcase($r['cmpControlTypeHandle']) . 'ComposerControlType';
			$sc = new $class();
			$sc->setPropertiesFromArray($r);
			return $sc;
		}
	}
	public static function getByID($cmpControlTypeID) {
		$db = Loader::db();
		$r = $db->GetRow('select cmpControlTypeID, cmpControlTypeHandle, cmpControlTypeName, pkgID from ComposerControlTypes where cmpControlTypeID = ?', array($cmpControlTypeID));
		if (is_array($r) && $r['cmpControlTypeHandle']) {
			$class = Loader::helper('text')->camelcase($r['cmpControlTypeHandle']) . 'ComposerControlType';
			$sc = new $class();
			$sc->setPropertiesFromArray($r);
			return $sc;
		}
	}	
	
	public static function add($cmpControlTypeHandle, $cmpControlTypeName, $pkg = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('insert into ComposerControlTypes (cmpControlTypeHandle, cmpControlTypeName, pkgID) values (?, ?, ?)', array($cmpControlTypeHandle, $cmpControlTypeName, $pkgID));
		return ComposerControlType::getByHandle($cmpControlTypeHandle);
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from ComposerControlTypes where cmpControlTypeID = ?', array($this->cmpControlTypeID));
	}
	
	public static function getList() {
		$db = Loader::db();
		$ids = $db->GetCol('select cmpControlTypeID from ComposerControlTypes order by cmpControlTypeName asc');
		$types = array();
		foreach($ids as $id) {
			$type = ComposerControlType::getByID($id);
			if (is_object($type)) {
				$types[] = $type;
			}
		}
		return $types;
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$ids = $db->GetCol('select cmpControlTypeID from ComposerControlTypes where pkgID = ? order by cmpControlTypeName asc', array($pkg->getPackageID()));
		$types = array();
		foreach($ids as $id) {
			$type = ComposerControlType::getByID($id);
			if (is_object($type)) {
				$types[] = $type;
			}
		}
		return $types;
	}

	public static function exportList($xml) {
		$list = self::getList();
		$nxml = $xml->addChild('composercontroltypes');
		
		foreach($list as $sc) {
			$activated = 0;
			$type = $nxml->addChild('type');
			$type->addAttribute('handle', $sc->getComposerControlTypeHandle());
			$type->addAttribute('name', $sc->getComposerControlTypeName());
			$type->addAttribute('package', $sc->getPackageHandle());
		}
	}
		

}