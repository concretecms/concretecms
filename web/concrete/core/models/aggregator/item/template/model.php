<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AggregatorItemTemplate extends Object {

	public static function getByID($agtID) {
		$db = Loader::db();
		$row = $db->GetRow('select AggregatorItemTemplates.agtID, agtHandle, pkgID, agtName, count(AggregatorItemTemplateFeatures.afeID) as afeTotal from AggregatorItemTemplates left join AggregatorItemTemplateFeatures on AggregatorItemTemplates.agtID = AggregatorItemTemplateFeatures.agtID where AggregatorItemTemplates.agtID = ?', array($agtID));
		if (isset($row['agtID'])) {
			$class = Loader::helper('text')->camelcase($row['agtHandle']) . 'AggregatorItemTemplate';
			$agt = new $class();
			$agt->setPropertiesFromArray($row);
			return $agt;
		}
	}
	
	public static function getByHandle($agtHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select agtID, agtHandle, pkgID, agtName from AggregatorItemTemplates where agtHandle = ?', array($agtHandle));
		if (isset($row['agtID'])) {
			$class = Loader::helper('text')->camelcase($row['agtHandle']) . 'AggregatorItemTemplate';
			$agt = new $class();
			$agt->setPropertiesFromArray($row);
			return $agt;
		}
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select agtID from AggregatorItemTemplates where pkgID = ? order by agtID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$agt = AggregatorItemTemplate::getByID($row['agtID']);
			if (is_object($agt)) {
				$agt[] = $agt;
			}
		}
		$r->Close();
		return $list;
	}	

	public static function getList() {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select agtID from AggregatorItemTemplates order by agtName asc');
		while ($row = $r->FetchRow()) {
			$agt = AggregatorItemTemplate::getByID($row['agtID']);
			if (is_object($agt)) {
				$list[] = $agt;
			}
		}
		$r->Close();
		return $list;
	}	
	
	public function getAggregatorItemTemplateID() {return $this->agtID;}
	public function getAggregatorItemTemplateHandle() {return $this->agtHandle;}
	public function getAggregatorItemTemplateName() {return $this->agtName;}
	public function getAggregatorItemTemplateFeaturesTotal() {return $this->afeTotal;}
	public function getPackageID() {return $this->pkgID;}
	public function getPackageHandle() {return PackageList::getHandle($this->pkgID);}

	public function addAggregatorItemTemplateFeature($fe) {
		$db = Loader::db();
		$no = $db->GetOne("select count(afeID) from AggregatorItemTemplateFeatures where agtID = ? and feID = ?", array($this->agtID, $fe->getFeatureID()));
		if ($no < 1) {
			$db->Execute('insert into AggregatorItemTemplateFeatures (agtID, feID) values (?, ?)', array($this->getAggregatorItemTemplateID(), $fe->getFeatureID()));
		}
	}

	public function getAggregatorItemTemplateFeatureObjects() {
		$db = Loader::db();
		$r = $db->Execute('select feID from AggregatorItemTemplateFeatures where agtID = ?', $this->getAggregatorItemTemplateID());
		$features = array();
		while ($row = $r->FetchRow()) {
			$fe = Feature::getByID($row['feID']);
			if (is_object($fe)) {
				$features[] = $fe;
			}
		}
		return $features;		
	}

	public static function add($agtHandle, $agtName, $pkg = false) {
		$db = Loader::db();
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}

		$db->Execute('insert into AggregatorItemTemplates (agtHandle, agtName, pkgID) values (?, ?, ?)', array($agtHandle, $agtName, $pkgID));
		$id = $db->Insert_ID();
		
		$agt = AggregatorItemTemplate::getByID($id);
		return $agt;
	}


	public function export($axml) {
		$agt = $axml->addChild('aggregatoritemtemplate');
		$agt->addAttribute('handle',$this->getAggregatorDataSourceHandle());
		$agt->addAttribute('name', $this->getAggregatorDataSourceName());
		$agt->addAttribute('package', $this->getPackageHandle());
		$features = $this->getAggregatorItemTemplateFeatureObjects();
		foreach($features as $fe) {
			$fe->export($agt);
		}
		return $agt;
	}

	public static function exportList($xml) {
		$axml = $xml->addChild('aggregatoritemtemplates');
		$db = Loader::db();
		$r = $db->Execute('select agtID from AggregatorItemTemplates order by agtID asc');
		$list = array();
		while ($row = $r->FetchRow()) {
			$agt = AggregatorItemTemplate::getByID($row['agtID']);
			if (is_object($agt)) {
				$list[] = $agt;
			}
		}
		foreach($list as $agt) {
			$agt->export($axml);
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from AggregatorItemTemplates where agtID = ?', array($this->agtID));
	}

	public function getAggregatorItemTemplateData(AggregatorItem $item) {
		$features = $this->getAggregatorItemTemplateFeatureObjects();
		$data = array();
		foreach($features as $f) {
			$method = 'getFeatureData' . Loader::helper('text')->camelcase($f->getFeatureHandle());
			if (method_exists($item, $method)) {
				$data[$f->getFeatureHandle()] = call_user_func(array($item, $method));
			}
		}
		return $data;
	}
	
		
}
