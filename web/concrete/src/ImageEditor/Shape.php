<?php
namespace Concrete\Core\ImageEditor;
use Loader;
use \Concrete\Core\Package\PackageList;
class Shape {
	public $scsID;
	public $scsHandle;
	public $scsName;
	public $scsDisplayOrder;
	public $pkgID;

	/**
 	 * Retrieves a list of shape objects.
 	 */
	public static function getList() {
		$db = Loader::db();
		$q  = $db->query('SELECT * FROM SystemImageEditorShapes');
		$cs = self::getSortedListFromQuery($q);
		return $cs;
	}

	/**
	 * Retrieves a list of shape objects by package
	 * this is used in package uninstall.
	 */
	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$q  = $db->query('SELECT * FROM SystemImageEditorShapes
							WHERE scsHandle=?',array($scsHandle));
		$cs = self::getSortedListFromQuery($q);
		return $cs;
	}

	/**
	 * Fancy way to sort using the display order
	 * Not super useful right now.
	 *
	 * This method naturally sorts first by display order, then by the orderby
	 * on the query.
	 */
	public static function getSortedListFromQuery($q) {
		$unsorted = array();
		while ($row = $q->FetchRow()) {
			$cs = self::load($row);
			$oid = $cs->getImageEditorShapeDisplayOrder();
			if (!$unsorted[$oid]) $unsorted[$oid] = array();
			$unsorted[$oid][] = $cs;
		}
		$sorted = array();
		foreach ($unsorted as $arr) {
			foreach ($arr as $v) {
				$sorted[] = $v;
			}
		}
		return $sorted;
	}

	/**
	 * Get the basic object
	 */
	public static function getByID($scsID) {
		$db = Loader::db();
		$q  = $db->query('SELECT * FROM SystemImageEditorShapes
							WHERE scsID=?',array($scsID));
		return self::load($q->FetchRow());
	}
	public static function getByHandle($scsHandle) {
		$db = Loader::db();
		$q  = $db->query('SELECT * FROM SystemImageEditorShapes
							WHERE scsHandle=?',array($scsHandle));
		return self::load($q->FetchRow());
	}
	public static function load($arr) {
		$obj = new static;
		foreach((array) $arr as $key=>$val) {
			$obj->{$key} = $val;
		}
		return $obj;
	}

	/**
	 * Retrieve Data
	 */
	public function getImageEditorShapeID() {
		return $this->scsID;
	}
	public function getImageEditorShapeHandle() {
		return $this->scsHandle;
	}
	public function getImageEditorShapeName() {
		return $this->scsName;
	}
	/** Returns the display name for this instance (localized and escaped accordingly to $format)
	* @param string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
	* @return string
	*/
	public function getImageEditorShapeDisplayName($format = 'html') {
		$value = tc('ImageEditorShapeName', $this->scsName);
		switch($format) {
			case 'html':
				return h($value);
			case 'text':
			default:
				return $value;
		}
	}
	public function getImageEditorShapeDisplayOrder() {
		return $this->scsDisplayOrder;
	}
	public function getPackageID() {
		return $this->pkgID;
	}
	public function getPackageHandle() {
		return $this->getPackageObject()->getPackageHandle();
	}
	public function getPackageObject() {
		return Package::getByID($this->getPackageID());
	}

	/**
	 * Basic management of these objects
	 */
	public static function add($scsHandle, $scsName, $pkg = false) {
		$db = Loader::db();
		$db->execute('INSERT INTO SystemImageEditorShapes
						(scsHandle,scsName,pkgID) VALUES (?,?,?)',
						array($scsHandle,$scsName,$pkg));
		return self::getByHandle($scsHandle);
	}

	public function delete() {
		$db = Loader::db();
		$db->execute('DELETE FROM SystemImageEditorShapes WHERE scsID=?',
			array($this->scsID));
		return true;
	}
}