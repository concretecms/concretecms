<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Model_AggregatorItemTemplate extends Object {

	abstract public function aggregatorItemTemplateControlsSlotDimensions();

	protected $feTotalScore;
	protected $feHandles;

	public function getAggregatorItemTemplateFeatureHandles() {
		if (!isset($this->feHandles)) {
			$db = Loader::db();
			$this->feHandles = $db->GetCol('select distinct feHandle from AggregatorItemTemplateFeatures at inner join Features fe on at.feID = fe.feID where agtID = ?', array($this->agtID));
		}
		return $this->feHandles;
	}

	public static function getByID($agtID) {
		$db = Loader::db();
		$row = $db->GetRow('select AggregatorItemTemplates.*, AggregatorItemTemplateTypes.agtTypeHandle from AggregatorItemTemplates inner join AggregatorItemTemplateTypes on AggregatorItemTemplateTypes.agtTypeID = AggregatorItemTemplates.agtTypeID where AggregatorItemTemplates.agtID = ?', array($agtID));
		if (isset($row['agtID'])) {
			$class = Loader::helper('text')->camelcase($row['agtTypeHandle']) . 'AggregatorItemTemplate';
			if ($row['agtHasCustomClass']) {
				$class = Loader::helper('text')->camelcase($row['agtHandle']) . $class;
			}
			$agt = new $class();
			$agt->setPropertiesFromArray($row);
			return $agt;
		}
	}
	
	public static function getByHandle($agtHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select agtID from AggregatorItemTemplates where agtHandle = ?', array($agtHandle));
		if (isset($row['agtID'])) {
			return AggregatorItemTemplate::getByID($row['agtID']);
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

	public static function getListByType(AggregatorItemTemplateType $type) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select agtID from AggregatorItemTemplates where agtTypeID = ? order by agtName asc', array($type->getAggregatorItemTemplateTypeID()));
		while ($row = $r->FetchRow()) {
			$agt = AggregatorItemTemplate::getByID($row['agtID']);
			if (is_object($agt)) {
				$list[] = $agt;
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
	public function getPackageID() {return $this->pkgID;}
	public function aggregatorItemTemplateHasCustomClass() {return $this->agtHasCustomClass;}
	public function aggregatorItemTemplateIsAlwaysDefault() {return $this->agtForceDefault;}
	public function getPackageHandle() {return PackageList::getHandle($this->pkgID);}
	public function getAggregatorItemTemplateFixedSlotWidth() {return $this->agtFixedSlotWidth;}
	public function getAggregatorItemTemplateFixedSlotHeight() {return $this->agtFixedSlotHeight;}
	public function getAggregatorItemTemplateTypeObject() {return AggregatorItemTemplateType::getByID($this->agtTypeID);}
	public function getAggregatorItemTemplateTypeID() {return $this->agtTypeID;}
	public function getAggregatorItemTemplateMinimumSlotHeight(AggregatorItem $item) {
		return 1;
	}
	public function getAggregatorItemTemplateMaximumSlotHeight(AggregatorItem $item) {
		return 2;
	}
	public function getAggregatorItemTemplateMinimumSlotWidth(AggregatorItem $item) {
		return 1;
	}
	public function getAggregatorItemTemplateMaximumSlotWidth(AggregatorItem $item) {
		return 3;
	}
	public function getAggregatorItemTemplateIconSRC() {
		$env = Environment::get();
		$type = $this->getAggregatorItemTemplateTypeObject();
		$path = $env->getURL(DIRNAME_ELEMENTS . '/' . DIRNAME_AGGREGATOR . '/' . DIRNAME_AGGREGATOR_ITEM_TEMPLATES . '/' . $type->getAggregatorItemTemplateTypeHandle() . '/' . $this->getAggregatorItemTemplateHandle() . '/' . FILENAME_AGGREGATOR_ITEM_TEMPLATE_ICON);
		return $path;
	}

	/** 
	 * This method is called by AggregatorItem when setting defaults
	 */
	public function getAggregatorItemTemplateSlotWidth(AggregatorItem $item) {
		if ($this->getAggregatorItemTemplateFixedSlotWidth()) {
			return $this->getAggregatorItemTemplateFixedSlotWidth();
		}

		$w = 0;
		$handles = $this->getAggregatorItemTemplateFeatureHandles();
		$assignments = AggregatorItemFeatureAssignment::getList($item);
		foreach($assignments as $as) {
			if (in_array($as->getFeatureDetailHandle(), $handles)) {
				$fd = $as->getFeatureDetailObject();
				if ($fd->getAggregatorItemSuggestedSlotWidth() > 0 && $fd->getAggregatorItemSuggestedSlotWidth() > $w) {
					$w = $fd->getAggregatorItemSuggestedSlotWidth();
				}
			}
		}

		if ($w) {
			return $w;
		}

		$wb = $this->getAggregatorItemTemplateMinimumSlotWidth($item);
		$wt = $this->getAggregatorItemTemplateMaximumSlotWidth($item);
		return mt_rand($wb, $wt);
	}

	public function getAggregatorItemTemplateSlotHeight(AggregatorItem $item) {
		if ($this->getAggregatorItemTemplateFixedSlotHeight()) {
			return $this->getAggregatorItemTemplateFixedSlotHeight();
		}

		$h = 0;
		$handles = $this->getAggregatorItemTemplateFeatureHandles();
		$assignments = AggregatorItemFeatureAssignment::getList($item);
		foreach($assignments as $as) {
			if (in_array($as->getFeatureDetailHandle(), $handles)) {
				$fd = $as->getFeatureDetailObject();
				if ($fd->getAggregatorItemSuggestedSlotHeight() > 0 && $fd->getAggregatorItemSuggestedSlotHeight() > $h) {
					$h = $fd->getAggregatorItemSuggestedSlotHeight();
				}
			}
		}


		if ($h) {
			return $h;
		}

		$hb = $this->getAggregatorItemTemplateMinimumSlotHeight($item);
		$ht = $this->getAggregatorItemTemplateMaximumSlotHeight($item);
		return mt_rand($hb, $ht);
	}

	public function addAggregatorItemTemplateFeature($fe) {
		$db = Loader::db();
		$no = $db->GetOne("select count(afeID) from AggregatorItemTemplateFeatures where agtID = ? and feID = ?", array($this->agtID, $fe->getFeatureID()));
		if ($no < 1) {
			$db->Execute('insert into AggregatorItemTemplateFeatures (agtID, feID) values (?, ?)', array($this->getAggregatorItemTemplateID(), $fe->getFeatureID()));
		}
	}

	public function getAggregatorTemplateFeaturesTotalScore() {
		if (!isset($this->feTotalScore)) {
			$db = Loader::db();
			$this->feTotalScore = $db->GetOne('select sum(feScore) from Features fe inner join AggregatorItemTemplateFeatures af on af.feID = fe.feID where af.agtID = ?', array($this->getAggregatorItemTemplateID()));
		}
		return $this->feTotalScore;
	}

	public function getAggregatorItemTemplateFeatureObjects() {
		$db = Loader::db();
		$r = $db->Execute('select feID from AggregatorItemTemplateFeatures where agtID = ?', array($this->getAggregatorItemTemplateID()));
		$features = array();
		while ($row = $r->FetchRow()) {
			$fe = Feature::getByID($row['feID']);
			if (is_object($fe)) {
				$features[] = $fe;
			}
		}
		return $features;		
	}

	public static function add(AggregatorItemTemplateType $type, $agtHandle, $agtName, $agtFixedSlotWidth, $agtFixedSlotHeight, $agtHasCustomClass = false, $agtForceDefault = false, $pkg = false) {
		$db = Loader::db();
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}

		$db->Execute('insert into AggregatorItemTemplates (agtTypeID, agtHandle, agtName, agtFixedSlotWidth, agtFixedSlotHeight, agtHasCustomClass, agtForceDefault, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?)', array($type->getAggregatorItemTemplateTypeID(), $agtHandle, $agtName, $agtFixedSlotWidth, $agtFixedSlotHeight, $agtHasCustomClass, $agtForceDefault, $pkgID));
		$id = $db->Insert_ID();
		
		$agt = AggregatorItemTemplate::getByID($id);
		return $agt;
	}


	public function export($axml) {
		$agt = $axml->addChild('aggregatoritemtemplate');
		$type = $this->getAggregatorItemTemplateTypeObject();
		$agt->addAttribute('handle',$this->getAggregatorItemTemplateHandle());
		$agt->addAttribute('name', $this->getAggregatorItemTemplateName());
		$agt->addAttribute('type', $type->getAggregatorItemTemplateTypeHandle());
		if ($this->aggregatorItemTemplateHasCustomClass()) {
			$agt->addAttribute('has-custom-class', true);
		} else {
			$agt->addAttribute('has-custom-class', false);
		}
		$agt->addAttribute('package', $this->getPackageHandle());
		$features = $this->getAggregatorItemTemplateFeatureObjects();
		foreach($features as $fe) {
			$fe->export($agt, false);
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
		$assignments = AggregatorItemFeatureAssignment::getList($item);
		$data = array();
		foreach($assignments as $as) {
			$fd = $as->getFeatureDetailObject();
			$key = $as->getFeatureDetailHandle();
			if (is_array($data[$key])) {
				$data[$key][] = $fd;
			} else if (array_key_exists($key, $data)) {
				$tmp = $data[$key];
				$data[$key] = array($tmp, $fd);
			} else {
				$data[$key] = $fd;
			}
		}
		return $data;
	}
	
		
}
