<?php defined('C5_EXECUTE') or die("Access Denied.");

abstract class Concrete5_Model_ComposerTargetType extends Object {

	abstract public function configureComposerTarget(Composer $cm, $post);
	
	public function getComposerTargetTypeName() {return $this->cmpTargetTypeName;}
	public function getComposerTargetTypeHandle() {return $this->cmpTargetTypeHandle;}
	public function getComposerTargetTypeID() { return $this->cmpTargetTypeID;}
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	public function getPackageObject() {return Package::getByID($this->pkgID);}

	public static function getByID($cmpTargetTypeID) {
		$db = Loader::db();
		$r = $db->GetRow('select cmpTargetTypeID, cmpTargetTypeHandle, cmpTargetTypeName, pkgID from ComposerTargetTypes where cmpTargetTypeID = ?', array($cmpTargetTypeID));
		if (is_array($r) && $r['cmpTargetTypeHandle']) {
			$class = Loader::helper('text')->camelcase($r['cmpTargetTypeHandle']) . 'ComposerTargetType';
			$sc = new $class();
			$sc->setPropertiesFromArray($r);
			return $sc;
		}
	}

	public static function getByHandle($cmpTargetTypeHandle) {
		$db = Loader::db();
		$r = $db->GetRow('select cmpTargetTypeID, cmpTargetTypeHandle, cmpTargetTypeName, pkgID from ComposerTargetTypes where cmpTargetTypeHandle = ?', array($cmpTargetTypeHandle));
		if (is_array($r) && $r['cmpTargetTypeHandle']) {
			$class = Loader::helper('text')->camelcase($r['cmpTargetTypeHandle']) . 'ComposerTargetType';
			$sc = new $class();
			$sc->setPropertiesFromArray($r);
			return $sc;
		}
	}
	
	public static function add($cmpTargetTypeHandle, $cmpTargetTypeName, $pkg = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('insert into ComposerTargetTypes (cmpTargetTypeHandle, cmpTargetTypeName, pkgID) values (?, ?, ?)', array($cmpTargetTypeHandle, $cmpTargetTypeName, $pkgID));
		return ComposerTargetType::getByHandle($cmpTargetTypeHandle);
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from ComposerTargetTypes where cmpTargetTypeID = ?', array($this->cmpTargetTypeID));
	}
	
	public static function getList() {
		$db = Loader::db();
		$ids = $db->GetCol('select cmpTargetTypeID from ComposerTargetTypes order by cmpTargetTypeName asc');
		$types = array();
		foreach($ids as $id) {
			$type = ComposerTargetType::getByID($id);
			if (is_object($type)) {
				$types[] = $type;
			}
		}
		return $types;
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$ids = $db->GetCol('select cmpTargetTypeID from ComposerTargetTypes where pkgID = ? order by cmpTargetTypeName asc', array($pkg->getPackageID()));
		$types = array();
		foreach($ids as $id) {
			$type = ComposerTargetType::getByID($id);
			if (is_object($type)) {
				$types[] = $type;
			}
		}
		return $types;
	}
	
	public static function exportList($xml) {
		$list = self::getList();
		$nxml = $xml->addChild('composertargettypes');
		
		foreach($list as $sc) {
			$activated = 0;
			$type = $nxml->addChild('type');
			$type->addAttribute('handle', $sc->getComposerTargetTypeHandle());
			$type->addAttribute('name', $sc->getComposerTargetTypeName());
			$type->addAttribute('package', $sc->getPackageHandle());
		}
	}
			
	public function hasOptionsForm() {
		$env = Environment::get();
		$rec = $env->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_COMPOSER . '/' . DIRNAME_COMPOSER_ELEMENTS_TARGET_TYPES . '/' . $this->getComposerTargetTypeHandle() . '.php', $this->getPackageHandle());
		return $rec->exists();
	}	

	public function includeOptionsForm($composer = false) {
		Loader::element(DIRNAME_COMPOSER . '/' . DIRNAME_COMPOSER_ELEMENTS_TARGET_TYPES . '/' . $this->getComposerTargetTypeHandle(), array('composer' => $composer), $this->getPackageHandle());
	}

}