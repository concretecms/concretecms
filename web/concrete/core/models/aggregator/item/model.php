<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Model_AggregatorItem extends Object {

	abstract public function loadDetails();
	abstract public function canViewAggregatorItem();

	protected $feHandles;
	protected $templates;

	public function getAggregatorItemID() {return $this->agiID;}
	public function getAggregatorDataSourceHandle() {return $this->agsHandle;}
	public function getAggregatorDataSourceID() {return $this->agsID;}
	public function getAggregatorItemPublicDateTime() {return $this->agiPublicDateTime;}
	public function getAggregatorItemTemplateID(AggregatorItemTemplateType $type) {
		if (!isset($this->templates)) {
			$this->loadAggregatorItemTemplates();
		}
		return $this->templates[$type->getAggregatorItemTemplateTypeID()];
	}

	public function getAggregatorItemTemplateObject(AggregatorItemTemplateType $type) {
		$agtID = $this->getAggregatorItemTemplateID($type);
		if ($agtID) {
			return AggregatorItemTemplate::getByID($agtID);
		}
	}
	public function getAggregatorItemTemplateHandle() {return $this->agtHandle;}
	public function getAggregatorItemSlotWidth() { return $this->agiSlotWidth; 	}
	public function getAggregatorItemSlotHeight() {	return $this->agiSlotHeight; }
	public function getAggregatorItemBatchTimestamp() {	return $this->agiBatchTimestamp; }
	public function getAggregatorItemBatchDisplayOrder() {	return $this->agiBatchDisplayOrder; }
	public function getAggregatorItemKey() { return $this->agiKey; }
	public function getAggregatorObject() { return Aggregator::getByID($this->agID); }
	public function getAggregatorID() { return $this->agID;}

	public function getAggregatorItemFeatureHandles() {
		if (!isset($this->feHandles)) {
			$db = Loader::db();
			$this->feHandles = $db->GetCol('select distinct feHandle from AggregatorItemFeatureAssignments afa inner join FeatureAssignments fa on afa.faID = fa.faID inner join Features fe on fa.feID = fe.feID where agiID = ?', array($this->agiID));
		}
		return $this->feHandles;
	}

	protected function loadAggregatorItemTemplates() {
		$this->templates = array();
		$db = Loader::db();
		$r = $db->Execute('select agtID, agtTypeID from AggregatorItemSelectedTemplates where agiID = ?', array($this->agiID));
		while ($row = $r->FetchRow()) {
			$this->templates[$row['agtTypeID']] = $row['agtID'];
		}
	}

	public function moveToNewAggregator(Aggregator $aggregator) {
		$db = Loader::db();
		$db->Execute('update AggregatorItems set agID = ? where agiID = ?', array($aggregator->getAggregatorID(), $this->agiID));
		$this->agID = $aggregator->getAggregatorID();
		$batch = $db->GetOne('select max(agiBatchTimestamp) from AggregatorItems where agiID = ?', array($this->agiID));
		$this->setAggregatorItemBatchTimestamp($batch);
		$this->setAggregatorItemBatchDisplayOrder(0);
	}

	public function setAggregatorItemTemplate(AggregatorItemTemplateType $type, AggregatorItemTemplate $template) {
		$db = Loader::db();
		$db->Execute('delete from AggregatorItemSelectedTemplates where agiID = ? and agtTypeID = ?', array($this->agiID, $type->getAggregatorItemTemplateTypeID()));
		$db->Execute('insert into AggregatorItemSelectedTemplates (agtTypeID, agiID, agtID) values (?, ?, ?)', array(
			$type->getAggregatorItemTemplateTypeID(), $this->agiID, $template->getAggregatorItemTemplateID()
		));
		$this->loadAggregatorItemTemplates();
	}

	public function setAggregatorItemBatchDisplayOrder($agiBatchDisplayOrder) {
		$db = Loader::db();
		$db->Execute('update AggregatorItems set agiBatchDisplayOrder = ? where agiID = ?', array($agiBatchDisplayOrder, $this->agiID));
		$this->agiBatchDisplayOrder = $agiBatchDisplayOrder;
	}

	public function setAggregatorItemBatchTimestamp($agiBatchTimestamp) {
		$db = Loader::db();
		$db->Execute('update AggregatorItems set agiBatchTimestamp = ? where agiID = ?', array($agiBatchTimestamp, $this->agiID));
		$this->agiBatchTimestamp = $agiBatchTimestamp;
	}

	public function setAggregatorItemSlotWidth($agiSlotWidth) {
		$db = Loader::db();
		$db->Execute('update AggregatorItems set agiSlotWidth = ? where agiID = ?', array($agiSlotWidth, $this->agiID));
		$this->agiSlotWidth = $agiSlotWidth;
	}

	public function setAggregatorItemSlotHeight($agiSlotHeight) {
		$db = Loader::db();
		$db->Execute('update AggregatorItems set agiSlotHeight = ? where agiID = ?', array($agiSlotHeight, $this->agiID));
		$this->agiSlotHeight = $agiSlotHeight;
	}

	public static function getByID($agiID) {
		$db = Loader::db();
		$r = $db->GetRow('select AggregatorItems.*, AggregatorDataSources.agsHandle from AggregatorItems inner join AggregatorDataSources on AggregatorItems.agsID = AggregatorDataSources.agsID where agiID = ?', array($agiID));
		if (is_array($r) && $r['agiID'] == $agiID) {
			if (!$r['agiIsDeleted']) {
				$class = Loader::helper('text')->camelcase($r['agsHandle']) . 'AggregatorItem';
				$ags = new $class();
				$ags->setPropertiesFromArray($r);
				$ags->loadDetails();
				return $ags;
			}
		}
	}

	public static function add(Aggregator $ag, AggregatorDataSource $ags, $agiPublicDateTime, $agiTitle, $agiKey, $agiSlotWidth = 1, $agiSlotHeight = 1) {
		$db = Loader::db();
		$agiDateTimeCreated = Loader::helper('date')->getSystemDateTime();
		$r = $db->Execute('insert into AggregatorItems (agID, agsID, agiDateTimeCreated, agiPublicDateTime, agiTitle, agiKey, agiSlotWidth, agiSlotHeight) values (?, ?, ?, ?, ?, ?, ?, ?)', array(
			$ag->getAggregatorID(),
			$ags->getAggregatorDataSourceID(), 
			$agiDateTimeCreated,
			$agiPublicDateTime, 
			$agiTitle,
			$agiKey,
			$agiSlotWidth,
			$agiSlotHeight
		));
		return AggregatorItem::getByID($db->Insert_ID());
	}


	public function duplicate(Aggregator $aggregator) {
		$db = Loader::db();
		$agID = $aggregator->getAggregatorID();
		$db->Execute('insert into AggregatorItems (agID, agsID, agiDateTimeCreated, agiPublicDateTime, agiTitle, agiKey, agiSlotWidth, agiSlotHeight, agiBatchTimestamp, agiBatchDisplayOrder) 
			values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
				$agID, $this->getAggregatorDataSourceID(), $this->agiDateTimeCreated, $this->agiPublicDateTime, 
				$this->agiTitle, $this->agiKey, $this->agiSlotWidth, $this->agiSlotHeight, $this->agiBatchTimestamp, $this->agiBatchDisplayOrder
			)
		);

		$this->loadAggregatorItemTemplates();
		$agiID = $db->Insert_ID();

		foreach($this->templates as $agtTypeID => $agtID) {
			$db->Execute('insert into AggregatorItemSelectedTemplates (agiID, agtTypeID, agtID) values (?, ?, ?)', array($agiID, $agtTypeID, $agtID));
		}

		$item = AggregatorItem::getByID($agiID);

		$assignments = AggregatorItemFeatureAssignment::getList($this);
		foreach($assignments as $as) {
			$item->copyFeatureAssignment($as);
		}

		return $item;
	}

	public function addFeatureAssignment($feHandle, $mixed) {
		$f = Feature::getbyHandle($feHandle);
		$fd = $f->getFeatureDetailObject($mixed);
		$as = AggregatorItemFeatureAssignment::add($f, $fd, $this);
		return $as;
	}

	public function copyFeatureAssignment(FeatureAssignment $fa) {
		$db = Loader::db();
		return AggregatorItemFeatureAssignment::add($fa->getFeatureObject(), $fa->getFeatureDetailObject(), $this);
	}

	protected function sortByFeatureScore($a, $b) {
		$ascore = $a->getAggregatorTemplateFeaturesTotalScore();
		$bscore = $b->getAggregatorTemplateFeaturesTotalScore();
		if ($ascore > $bscore) {
			return -1;
		} else if ($ascore < $bscore) {
			return 1;
		} else {
			return 0;
		}
	}

	protected function weightByFeatureScore($a, $b) {
		$ascore = $a->getAggregatorTemplateFeaturesTotalScore();
		$bscore = $b->getAggregatorTemplateFeaturesTotalScore();
		return mt_rand(0, ($ascore+$bscore)) > $ascore ? 1 : -1;
	}

	public function setAutomaticAggregatorItemTemplate() {
		$arr = Loader::helper('array');
		$db = Loader::db();
		$myFeatureHandles = $this->getAggregatorItemFeatureHandles();

		// we loop through and do it for all installed aggregator item template types
		$types = AggregatorItemTemplateType::getList();
		foreach($types as $type) {
			$matched = array();
			$r = $db->Execute('select agtID from AggregatorItemTemplates where agtTypeID = ?', array($type->getAggregatorItemTemplateTypeID()));
			while ($row = $r->FetchRow()) {
				$templateFeatureHandles = $db->GetCol('select feHandle from Features f inner join AggregatorItemTemplateFeatures af on f.feID = af.feID where agtID = ?', array($row['agtID']));
				if ($arr->subset($templateFeatureHandles, $myFeatureHandles)) {
					$matched[] = AggregatorItemTemplate::getByID($row['agtID']);
				}
			}

			usort($matched, array($this, 'sortByFeatureScore'));
			if (is_object($matched[0]) && $matched[0]->aggregatorItemTemplateIsAlwaysDefault()) {
				$template = $matched[0];
			} else {
				// we do some fun randomization math.
				usort($matched, array($this, 'weightByFeatureScore'));
				$template = $matched[0];
			}
			if (is_object($template)) {
				$this->setAggregatorItemTemplate($type, $template);
				if ($template->aggregatorItemTemplateControlsSlotDimensions()) {
					$this->setAggregatorItemSlotWidth($template->getAggregatorItemTemplateSlotWidth($this));
					$this->setAggregatorItemSlotHeight($template->getAggregatorItemTemplateSlotHeight($this));
				}
			}
		}
	}

	public function itemSupportsAggregatorItemTemplate(AggregatorItemTemplate $template) {
		// checks to see if all the features necessary to implement the template are present in this item.
		$templateFeatures = $template->getAggregatorItemTemplateFeatureHandles();
		$itemFeatures = $this->getAggregatorItemFeatureHandles();
		$features = array_intersect($templateFeatures, $itemFeatures);
		return count($features) == count($templateFeatures);
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from AggregatorItems where agiID = ?', array($this->agiID));
		$db->Execute('delete from AggregatorItemSelectedTemplates where agiID = ?', array($this->agiID));
		$assignments = AggregatorItemFeatureAssignment::getList($this);
		foreach($assignments as $as) {
			$as->delete();
		}
	}

	public function deactivate() {
		$db = Loader::db();
		$db->Execute('update AggregatorItems set agiIsDeleted = 1 where agiID = ?', array($this->agiID));
	}

	public function render(AggregatorItemTemplateType $type) {
		$t = $this->getAggregatorItemTemplateObject($type);
		if (is_object($t)) {
			$data = $t->getAggregatorItemTemplateData($this);
			$env = Environment::get();
			extract($data);
			Loader::element(DIRNAME_AGGREGATOR . '/' . DIRNAME_AGGREGATOR_ITEM_TEMPLATES . '/' . $type->getAggregatorItemTemplateTypeHandle() . '/' . $t->getAggregatorItemTemplateHandle() . '/view', $data);
		}
	}

}
