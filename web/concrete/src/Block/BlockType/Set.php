<?php
namespace Concrete\Core\Block\BlockType;
use \Concrete\Core\Foundation\Object;
use Loader;
use Core;
use Environment;
use CacheLocal;
use \Concrete\Core\Package\PackageList;
class Set extends Object {

	public static function getByID($btsID) {

        $cache = Core::make('cache/request');
    	$identifier = sprintf('block/type/set/%s', $btsID);
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            return $item->get();
        }

        $item->lock();

		$db = Loader::db();
		$row = $db->GetRow('select btsID, btsHandle, pkgID, btsName from BlockTypeSets where btsID = ?', array($btsID));
		if (isset($row['btsID'])) {
			$akc = new static();
			$akc->setPropertiesFromArray($row);
            $item->set($akc);
			return $akc;
		}
	}

	public static function getByHandle($btsHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select btsID, btsHandle, pkgID, btsName from BlockTypeSets where btsHandle = ?', array($btsHandle));
		if (isset($row['btsID'])) {
			$akc = new static();
			$akc->setPropertiesFromArray($row);
			return $akc;
		}
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select btsID from BlockTypeSets where pkgID = ? order by btsID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = static::getByID($row['btsID']);
		}
		$r->Close();
		return $list;
	}

	public static function getList() {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select btsID from BlockTypeSets order by btsDisplayOrder asc');
		while ($row = $r->FetchRow()) {
			$list[] = static::getByID($row['btsID']);
		}
		$r->Close();
		return $list;
	}

	public function getBlockTypeSetID() {return $this->btsID;}
	public function getBlockTypeSetHandle() {return $this->btsHandle;}
	public function getBlockTypeSetName() {return $this->btsName;}
	public function getPackageID() {return $this->pkgID;}
	public function getPackageHandle() {return PackageList::getHandle($this->pkgID);}

	/** Returns the display name for this instance (localized and escaped accordingly to $format)
	* @param string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
	* @return string
	*/
	public function getBlockTypeSetDisplayName($format = 'html')
	{
		$value = tc('BlockTypeSetName', $this->btsName);
		switch($format) {
			case 'html':
				return h($value);
			case 'text':
			default:
				return $value;
		}
	}
	public function updateBlockTypeSetName($btsName) {
		$this->btsName = $btsName;
		$db = Loader::db();
		$db->Execute("update BlockTypeSets set btsName = ? where btsID = ?", array($btsName, $this->btsID));
	}

	public function updateBlockTypeSetHandle($btsHandle) {
		$this->btsHandle = $btsHandle;
		$db = Loader::db();
		$db->Execute("update BlockTypeSets set btsHandle = ? where btsID = ?", array($btsHandle, $this->btsID));
	}

	public function addBlockType(BlockType $bt) {
		$db = Loader::db();
		$no = $db->GetOne("select count(btID) from BlockTypeSetBlockTypes where btID = ? and btsID = ?", array($bt->getBlockTypeID(), $this->getBlockTypeSetID()));
		if ($no < 1) {
			$types = $db->GetOne('select count(btID) from BlockTypeSetBlockTypes where btsID = ?', array($this->getBlockTypeSetID()));
			$displayOrder = 0;
			if ($types > 0) {
				$displayOrder = $db->GetOne('select max(displayOrder) from BlockTypeSetBlockTypes where btsID = ?', array($this->getBlockTypeSetID()));
				$displayOrder++;
			}

			$db->Execute('insert into BlockTypeSetBlockTypes (btsID, btID, displayOrder) values (?, ?, ?)', array($this->getBlockTypeSetID(), $bt->getBlockTypeID(), $displayOrder));
		}
	}

	public function clearBlockTypes() {
		$db = Loader::db();
		$db->Execute('delete from BlockTypeSetBlockTypes where btsID = ?', array($this->btsID));
	}

	public static function add($btsHandle, $btsName, $pkg = false) {
		$db = Loader::db();
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$sets = $db->GetOne('select count(btsID) from BlockTypeSets');
		$btsDisplayOrder = 0;
		if ($sets > 0) {
			$btsDisplayOrder = $db->GetOne('select max(btsDisplayOrder) from BlockTypeSets');
			$btsDisplayOrder++;
		}

		$db->Execute('insert into BlockTypeSets (btsHandle, btsName, pkgID) values (?, ?, ?)', array($btsHandle, $btsName, $pkgID));
		$id = $db->Insert_ID();

		$bs = static::getByID($id);
		return $bs;
	}


	public function export($axml) {
		$bset = $axml->addChild('blocktypeset');
		$bset->addAttribute('handle',$this->getBlockTypeSetHandle());
		$bset->addAttribute('name', $this->getBlockTypeSetName());
		$bset->addAttribute('package', $this->getPackageHandle());
		$types = $this->getBlockTypes();
		foreach($types as $bt) {
			$typenode = $bset->addChild('blocktype');
			$typenode->addAttribute('handle', $bt->getBlockTypeHandle());
		}
		return $bset;
	}

	public static function exportList($xml) {
		$bxml = $xml->addChild('blocktypesets');
		$db = Loader::db();
		$r = $db->Execute('select btsID from BlockTypeSets order by btsID asc');
		$list = array();
		while ($row = $r->FetchRow()) {
			$list[] = static::getByID($row['btsID']);
		}
		foreach($list as $bs) {
			$bs->export($bxml);
		}
	}

	public function getBlockTypes() {
		$db = Loader::db();
		$r = $db->Execute('select btID from BlockTypeSetBlockTypes where btsID = ? order by displayOrder asc', $this->getBlockTypeSetID());
		$types = array();
		while ($row = $r->FetchRow()) {
			$bt = BlockType::getByID($row['btID']);
			if (is_object($bt)) {
				$types[] = $bt;
			}
		}
		return $types;
	}

	public function get() {
		$db = Loader::db();
		$r = $db->Execute('select btID from BlockTypeSetBlockTypes where btsID = ? order by displayOrder asc', $this->getBlockTypeSetID());
		$types = array();
		while ($row = $r->FetchRow()) {
			$bt = BlockType::getByHandle($row['btID']);
			if (is_object($bt)) {
				$types[] = $bt;
			}
		}
		return $types;
	}

	public function contains($bt) {
		$db = Loader::db();
		$r = $db->GetOne('select count(*) from BlockTypeSetBlockTypes where btsID = ? and btID = ?', array($this->getBlockTypeSetID(), $bt->getBlockTypeID()));
		return $r > 0;
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from BlockTypeSets where btsID = ?', array($this->getBlockTypeSetID()));
		$db->Execute('delete from BlockTypeSetBlockTypes where btsID = ?', array($this->getBlockTypeSetID()));
	}

	public function deleteKey($bt) {
		$db = Loader::db();
		$db->Execute('delete from BlockTypeSetBlockTypes where btsID = ? and btID = ?', array($this->getBlockTypeSetID(), $bt->getBlockTypeID()));
		$this->rescanDisplayOrder();
	}

	protected function rescanDisplayOrder() {
		$db = Loader::db();
		$do = 1;
		$r = $db->Execute('select btID from BlockTypeSetBlockTypes where btsID = ? order by displayOrder asc', $this->getBlockTypeSetID());
		while ($row = $r->FetchRow()) {
			$db->Execute('update BlockTypeSetBlockTypes set displayOrder = ? where btID = ? and btsID = ?', array($do, $row['btID'], $this->getBlockTypeSetID()));
			$do++;
		}
	}

}
