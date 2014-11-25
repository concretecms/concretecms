<?php
namespace Concrete\Core\Gathering\Item\Template;
use Concrete\Core\Feature\Assignment\GatheringItemAssignment;
use Concrete\Core\Feature\Feature;
use Concrete\Core\Gathering\Item\Item;
use Loader;
use \Concrete\Core\Package\PackageList;
use Core;
use \Concrete\Core\Foundation\Object;
abstract class Template extends Object {

	abstract public function gatheringItemTemplateControlsSlotDimensions();

	protected $feTotalScore;
	protected $feHandles;

	public function getGatheringItemTemplateFeatureHandles() {
		if (!isset($this->feHandles)) {
			$db = Loader::db();
			$this->feHandles = $db->GetCol('select distinct feHandle from GatheringItemTemplateFeatures at inner join Features fe on at.feID = fe.feID where gatID = ?', array($this->gatID));
		}
		return $this->feHandles;
	}

	public static function getByID($gatID) {
		$db = Loader::db();
		$row = $db->GetRow('select GatheringItemTemplates.*, GatheringItemTemplateTypes.gatTypeHandle from GatheringItemTemplates inner join GatheringItemTemplateTypes on GatheringItemTemplateTypes.gatTypeID = GatheringItemTemplates.gatTypeID where GatheringItemTemplates.gatID = ?', array($gatID));
		if (isset($row['gatID'])) {
			$ns = Loader::helper('text')->camelcase($row['gatTypeHandle']);
			$class = 'Template';
			if ($row['gatHasCustomClass']) {
				$class = Loader::helper('text')->camelcase($row['gatHandle']) . $class;
			}
			$className = '\\Concrete\\Core\\Gathering\\Item\\Template\\' . $ns . '\\' . $class;
			$agt = Core::make($className);
			$agt->setPropertiesFromArray($row);
			return $agt;
		}
	}

	public static function getByHandle($gatHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select gatID from GatheringItemTemplates where gatHandle = ?', array($gatHandle));
		if (isset($row['gatID'])) {
			return static::getByID($row['gatID']);
		}
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select gatID from GatheringItemTemplates where pkgID = ? order by gatID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$agt = static::getByID($row['gatID']);
			if (is_object($agt)) {
				$agt[] = $agt;
			}
		}
		$r->Close();
		return $list;
	}

	public static function getListByType(\Concrete\Core\Gathering\Item\Template\Type $type) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select gatID from GatheringItemTemplates where gatTypeID = ? order by gatName asc', array($type->getGatheringItemTemplateTypeID()));
		while ($row = $r->FetchRow()) {
			$agt = static::getByID($row['gatID']);
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
		$r = $db->Execute('select gatID from GatheringItemTemplates order by gatName asc');
		while ($row = $r->FetchRow()) {
			$agt = static::getByID($row['gatID']);
			if (is_object($agt)) {
				$list[] = $agt;
			}
		}
		$r->Close();
		return $list;
	}

	public function getGatheringItemTemplateID() {return $this->gatID;}
	public function getGatheringItemTemplateHandle() {return $this->gatHandle;}
	public function getGatheringItemTemplateName() {return $this->gatName;}
	public function getPackageID() {return $this->pkgID;}
	public function gatheringItemTemplateHasCustomClass() {return $this->gatHasCustomClass;}
	public function gatheringItemTemplateIsAlwaysDefault() {return $this->gatForceDefault;}
	public function getPackageHandle() {return PackageList::getHandle($this->pkgID);}
	public function getGatheringItemTemplateFixedSlotWidth() {return $this->gatFixedSlotWidth;}
	public function getGatheringItemTemplateFixedSlotHeight() {return $this->gatFixedSlotHeight;}
	public function getGatheringItemTemplateTypeObject() {return Type::getByID($this->gatTypeID);}
	public function getGatheringItemTemplateTypeID() {return $this->gatTypeID;}
	public function getGatheringItemTemplateMinimumSlotHeight(GatheringItem $item) {
		return 1;
	}
	public function getGatheringItemTemplateMaximumSlotHeight(GatheringItem $item) {
		return 2;
	}
	public function getGatheringItemTemplateMinimumSlotWidth(GatheringItem $item) {
		return 1;
	}
	public function getGatheringItemTemplateMaximumSlotWidth(GatheringItem $item) {
		return 3;
	}
	public function getGatheringItemTemplateIconSRC() {
		$env = Environment::get();
		$type = $this->getGatheringItemTemplateTypeObject();
		$path = $env->getURL(DIRNAME_ELEMENTS . '/' . DIRNAME_GATHERING . '/' . DIRNAME_GATHERING_ITEM_TEMPLATES . '/' . $type->getGatheringItemTemplateTypeHandle() . '/' . $this->getGatheringItemTemplateHandle() . '/' . FILENAME_GATHERING_ITEM_TEMPLATE_ICON);
		return $path;
	}

	/**
	 * This method is called by GatheringItem when setting defaults
	 */
	public function getGatheringItemTemplateSlotWidth(GatheringItem $item) {
		if ($this->getGatheringItemTemplateFixedSlotWidth()) {
			return $this->getGatheringItemTemplateFixedSlotWidth();
		}

		$w = 0;
		$handles = $this->getGatheringItemTemplateFeatureHandles();
		$assignments = \Concrete\Core\Feature\Assignment\GatheringItemAssignment::getList($item);
		foreach($assignments as $as) {
			if (in_array($as->getFeatureDetailHandle(), $handles)) {
				$fd = $as->getFeatureDetailObject();
				if ($fd->getGatheringItemSuggestedSlotWidth() > 0 && $fd->getGatheringItemSuggestedSlotWidth() > $w) {
					$w = $fd->getGatheringItemSuggestedSlotWidth();
				}
			}
		}

		if ($w) {
			return $w;
		}

		$wb = $this->getGatheringItemTemplateMinimumSlotWidth($item);
		$wt = $this->getGatheringItemTemplateMaximumSlotWidth($item);
		return mt_rand($wb, $wt);
	}

	public function getGatheringItemTemplateSlotHeight(Item $item) {
		if ($this->getGatheringItemTemplateFixedSlotHeight()) {
			return $this->getGatheringItemTemplateFixedSlotHeight();
		}

		$h = 0;
		$handles = $this->getGatheringItemTemplateFeatureHandles();
		$assignments = GatheringItemAssignment::getList($item);
		foreach($assignments as $as) {
			if (in_array($as->getFeatureDetailHandle(), $handles)) {
				$fd = $as->getFeatureDetailObject();
				if ($fd->getGatheringItemSuggestedSlotHeight() > 0 && $fd->getGatheringItemSuggestedSlotHeight() > $h) {
					$h = $fd->getGatheringItemSuggestedSlotHeight();
				}
			}
		}


		if ($h) {
			return $h;
		}

		$hb = $this->getGatheringItemTemplateMinimumSlotHeight($item);
		$ht = $this->getGatheringItemTemplateMaximumSlotHeight($item);
		return mt_rand($hb, $ht);
	}

	public function addGatheringItemTemplateFeature($fe) {
		$db = Loader::db();
		$no = $db->GetOne("select count(gfeID) from GatheringItemTemplateFeatures where gatID = ? and feID = ?", array($this->gatID, $fe->getFeatureID()));
		if ($no < 1) {
			$db->Execute('insert into GatheringItemTemplateFeatures (gatID, feID) values (?, ?)', array($this->getGatheringItemTemplateID(), $fe->getFeatureID()));
		}
	}

	public function getGatheringTemplateFeaturesTotalScore() {
		if (!isset($this->feTotalScore)) {
			$db = Loader::db();
			$this->feTotalScore = $db->GetOne('select sum(feScore) from Features fe inner join GatheringItemTemplateFeatures af on af.feID = fe.feID where af.gatID = ?', array($this->getGatheringItemTemplateID()));
		}
		return $this->feTotalScore;
	}

	public function getGatheringItemTemplateFeatureObjects() {
		$db = Loader::db();
		$r = $db->Execute('select feID from GatheringItemTemplateFeatures where gatID = ?', array($this->getGatheringItemTemplateID()));
		$features = array();
		while ($row = $r->FetchRow()) {
			$fe = Feature::getByID($row['feID']);
			if (is_object($fe)) {
				$features[] = $fe;
			}
		}
		return $features;
	}

	public static function add(\Concrete\Core\Gathering\Item\Template\Type $type, $gatHandle, $gatName, $gatFixedSlotWidth, $gatFixedSlotHeight, $gatHasCustomClass = false, $gatForceDefault = false, $pkg = false) {
		$db = Loader::db();
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}

		$db->Execute('insert into GatheringItemTemplates (gatTypeID, gatHandle, gatName, gatFixedSlotWidth, gatFixedSlotHeight, gatHasCustomClass, gatForceDefault, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?)', array($type->getGatheringItemTemplateTypeID(), $gatHandle, $gatName, $gatFixedSlotWidth, $gatFixedSlotHeight, intval($gatHasCustomClass), intval($gatForceDefault), $pkgID));
		$id = $db->Insert_ID();

		$agt = static::getByID($id);
		return $agt;
	}


	public function export($axml) {
		$agt = $axml->addChild('gatheringitemtemplate');
		$type = $this->getGatheringItemTemplateTypeObject();
		$agt->addAttribute('handle',$this->getGatheringItemTemplateHandle());
		$agt->addAttribute('name', Core::make('helper/text')->entities($this->getGatheringItemTemplateName()));
		$agt->addAttribute('type', $type->getGatheringItemTemplateTypeHandle());
		if ($this->gatheringItemTemplateHasCustomClass()) {
			$agt->addAttribute('has-custom-class', true);
		} else {
			$agt->addAttribute('has-custom-class', false);
		}
		$agt->addAttribute('package', $this->getPackageHandle());
		$features = $this->getGatheringItemTemplateFeatureObjects();
		foreach($features as $fe) {
			$fe->export($agt, false);
		}
		return $agt;
	}

	public static function exportList($xml) {
		$axml = $xml->addChild('gatheringitemtemplates');
		$db = Loader::db();
		$r = $db->Execute('select gatID from GatheringItemTemplates order by gatID asc');
		$list = array();
		while ($row = $r->FetchRow()) {
			$agt = static::getByID($row['gatID']);
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
		$db->Execute('delete from GatheringItemTemplates where gatID = ?', array($this->gatID));
	}

	public function getGatheringItemTemplateData(\Concrete\Gathering\Item $item) {
		$assignments = \Concrete\Core\Gathering\Feature\Assignment::getList($item);
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
