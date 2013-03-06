<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Model_AggregatorItem extends Object {

	abstract public function loadDetails();

	public function getAggregatorItemID() {return $this->agiID;}
	public function getAggregatorDataSourceHandle() {return $this->agsHandle;}
	public function getAggregatorItemPublicDateTime() {return $this->agiPublicDateTime;}
	public function getAggregatorItemTemplateID() {return $this->agtID;}
	public function getAggregatorItemTemplateHandle() {return $this->agtHandle;}
	public function getAggregatorItemSlotWidth() { return $this->agiSlotWidth; 	}
	public function getAggregatorItemSlotHeight() {	return $this->agiSlotHeight; }

	public function getAggregatorItemFeatureHandles() {
		$db = Loader::db();
		$handles = $db->GetCol('select distinct feHandle from AggregatorItemFeatureAssignments afa inner join FeatureAssignments fa on afa.faID = fa.faID inner join Features fe on fa.feID = fe.feID where agiID = ?', array($this->agiID));
		return $handles;
	}

	public function setAggregatorItemTemplateID($agtID) {
		$db = Loader::db();
		$db->Execute('update AggregatorItems set agtID = ? where agiID = ?', array($agtID, $this->agiID));
		$this->agtID = $agtID;
	}

	public static function getByID($agiID) {
		$db = Loader::db();
		$r = $db->GetRow('select AggregatorItems.*, AggregatorItemTemplates.agtHandle, AggregatorDataSources.agsHandle from AggregatorItems inner join AggregatorDataSources on AggregatorItems.agsID = AggregatorDataSources.agsID left join AggregatorItemTemplates on AggregatorItems.agtID = AggregatorItemTemplates.agtID where agiID = ?', array($agiID));
		if (is_array($r) && $r['agiID'] == $agiID) {
			$class = Loader::helper('text')->camelcase($r['agsHandle']) . 'AggregatorItem';
			$ags = new $class();
			$ags->setPropertiesFromArray($r);
			$ags->loadDetails();
			return $ags;
		}
	}

	public static function add(Aggregator $ag, AggregatorDataSource $ags, $agiPublicDateTime, $agiTitle, $agiSlotWidth = 1, $agiSlotHeight = 1) {
		$db = Loader::db();
		$agiDateTimeCreated = Loader::helper('date')->getSystemDateTime();
		$r = $db->Execute('insert into AggregatorItems (agID, agsID, agiDateTimeCreated, agiPublicDateTime, agiTitle, agiSlotWidth, agiSlotHeight) values (?, ?, ?, ?, ?, ?, ?)', array(
			$ag->getAggregatorID(),
			$ags->getAggregatorDataSourceID(), 
			$agiDateTimeCreated,
			$agiPublicDateTime, 
			$agiTitle,
			$agiSlotWidth,
			$agiSlotHeight
		));
		return AggregatorItem::getByID($db->Insert_ID());
	}

	public function addFeatureAssignment($feHandle, $mixed) {
		$f = Feature::getbyHandle($feHandle);
		$fd = $f->getFeatureDetailObject($mixed);
		$as = AggregatorItemFeatureAssignment::add($f, $fd, $this);
		return $as;
	}

	public function copyFeatureAssignment(FeatureAssignment $fa) {
		$db = Loader::db();
		$db->Replace('AggregatorItemFeatureAssignments', array('faID' => $fa->getFeatureAssignmentID(), 'agiID' => $this->getAggregatorItemID()), array('agiID', 'faID',), true);
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

	public function setDefaultAggregatorItemTemplate() {
		$arr = Loader::helper('array');
		$db = Loader::db();
		$myFeatureHandles = $this->getAggregatorItemFeatureHandles();
		$matched = array();
		$r = $db->Execute('select agtID from AggregatorItemTemplates');
		while ($row = $r->FetchRow()) {
			$templateFeatureHandles = $db->GetCol('select feHandle from Features f inner join AggregatorItemTemplateFeatures af on f.feID = af.feID where agtID = ?', array($row['agtID']));
			if ($arr->subset($templateFeatureHandles, $myFeatureHandles)) {
				$matched[] = AggregatorItemTemplate::getByID($row['agtID']);
			}
		}
		usort($matched, array($this, 'sortByFeatureScore'));
		if (is_object($matched[0])) {
			$this->setAggregatorItemTemplateID($matched[0]->getAggregatorItemTemplateID());
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from AggregatorItems where agiID = ?', array($this->agiID));
		$db->Execute('delete from AggregatorItemFeatureAssignments where agiID = ?', array($this->agiID));
	}

	public function render() {
		$t = AggregatorItemTemplate::getByID($this->agtID);
		$data = $t->getAggregatorItemTemplateData($this);
		$env = Environment::get();
		extract($data);
		// we can't just use Loader::element because it strips off .php of the filename. Lame.
		$path = $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_AGGREGATOR . '/' . DIRNAME_AGGREGATOR_ITEM_TEMPLATES . '/' . $this->getAggregatorItemTemplateHandle() . '/' . FILENAME_AGGREGATOR_VIEW);
		include($path);
	}

}
