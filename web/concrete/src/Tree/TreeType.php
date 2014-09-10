<?php
namespace Concrete\Core\Tree;
use \Concrete\Core\Foundation\Object;
use Loader;
use \Concrete\Core\Package\PackageList;
class TreeType extends Object {

	public function getTreeTypeID() {
		return $this->treeTypeID;
	}
	public function getTreeTypeHandle() {
		return $this->treeTypeHandle;
	}
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}

	public function add($treeTypeHandle, $pkg = false) {

		$pkgID = 0;
		$db = Loader::db();
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}

		$r = $db->query("insert into TreeTypes (treeTypeHandle, pkgID) values (?, ?)", array(
			$treeTypeHandle, $pkgID
		));

		$treeTypeID = $db->Insert_ID();
		return TreeType::getByID($treeTypeID);
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from TreeTypes where treeTypeID = ?', array($this->treeTypeID));
	}

	public static function getByID($treeTypeID) {
		$db = Loader::db();
		$row = $db->GetRow('select * from TreeTypes where treeTypeID = ?', array($treeTypeID));
		if (is_array($row) && $row['treeTypeID']) {
			$type = new TreeType();
			$type->setPropertiesFromArray($row);
			return $type;
		}
	}

	public static function getByHandle($treeTypeHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select * from TreeTypes where treeTypeHandle = ?', array($treeTypeHandle));
		if (is_array($row) && $row['treeTypeHandle']) {
			$type = new TreeType();
			$type->setPropertiesFromArray($row);
			return $type;
		}
	}

	public function getTreeTypeClass() {
		$txt = Loader::helper('text');
		$className = '\\Concrete\\Core\\Tree\\Type\\' . $txt->camelcase($this->treeTypeHandle);
		return $className;
	}


	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select treeTypeID from TreeTypes where pkgID = ? order by treeTypeID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = TreeType::getByID($row['treeTypeID']);
		}


		$r->Close();
		return $list;
	}

}
