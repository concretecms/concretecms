<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AggregatorItemTemplateType extends Object {

	public function getAggregatorItemTemplateTypeID() {return $this->agtTypeID;}
	public function getAggregatorItemTemplateTypeHandle() {return $this->agtTypeHandle;}
	public function getAggregatorItemTemplateTypeName() {
		return Loader::helper('text')->unhandle($this->agtTypeHandle);
	}

	public static function getByID($agtTypeID) {
		$db = Loader::db();
		$row = $db->GetRow('select agtTypeID, pkgID, agtTypeHandle from AggregatorItemTemplateTypes where agtTypeID = ?', array($agtTypeID));
		if ($row['agtTypeID']) {
			$wt = new AggregatorItemTemplateType();
			$wt->setPropertiesFromArray($row);
			return $wt;
		}
	}

	public static function getList() {
		$agItemTemplateTypeList = CacheLocal::getEntry('agItemTemplateTypeList', false);
		if ($agItemTemplateTypeList != false) {
			return $agItemTemplateTypeList;
		}		

		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select agtTypeID from AggregatorItemTemplateTypes order by agtTypeID asc');

		while ($row = $r->FetchRow()) {
			$type = AggregatorItemTemplateType::getByID($row['agtTypeID']);
			if (is_object($type)) {
				$list[] = $type;
			}
		}
		
		$r->Close();
		CacheLocal::set('agItemTemplateTypeList', false, $list);
		return $list;
	}
	
	public static function exportList($xml) {
		$agtypes = AggregatorItemTemplateType::getList();
		$db = Loader::db();
		$axml = $xml->addChild('aggregatoritemtemplatetypes');
		foreach($agtypes as $agt) {
			$atype = $axml->addChild('aggregatoritemtemplatetype');
			$atype->addAttribute('handle', $agt->getAggregatorItemTemplateTypeHandle());
			$atype->addAttribute('package', $wt->getPackageHandle());
		}
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute("delete from AggregatorItemTemplateTypes where agtTypeID = ?", array($this->agtTypeID));
	}
	
	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select agtTypeID from AggregatorItemTemplateTypes where pkgID = ? order by agtTypeID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$type = AggregatorItemTemplateType::getByID($row['agtTypeID']);
			if (is_object($type)) {
				$list[] = $type;
			}
		}
		$r->Close();
		return $list;
	}	
	
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	
	public static function getByHandle($agtTypeHandle) {
		$db = Loader::db();
		$agtTypeID = $db->GetOne('select agtTypeID from AggregatorItemTemplateTypes where agtTypeHandle = ?', array($agtTypeHandle));
		if ($agtTypeID > 0) {
			return self::getByID($agtTypeID);
		}
	}
	
	public static function add($agtTypeHandle, $pkg = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('insert into AggregatorItemTemplateTypes (agtTypeHandle, pkgID) values (?, ?)', array($agtTypeHandle, $pkgID));
		$id = $db->Insert_ID();
		$est = AggregatorItemTemplateType::getByID($id);
		return $est;
	}
	
}
