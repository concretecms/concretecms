<?php
namespace Concrete\Core\Feature\Assignment;
use \Concrete\Core\Foundation\Object;
use Loader;
use Core;
use \Concrete\Core\Feature\Feature;
use \Concrete\Core\Feature\Category\Category as FeatureCategory;
use \Concrete\Core\Feature\Detail\Detail as FeatureDetail;
abstract class Assignment extends Object {

	abstract public function loadDetails($mixed);
	abstract public static function getList($mixed);
	abstract public static function getFeature($feHandle, $mixed);
	abstract public static function add(Feature $fe, FeatureDetail $fd, $mixed);

	public static function addAssignment(Feature $fe, FeatureCategory $fc, FeatureDetail $fd, $mixed) {
		$db = Loader::db();
		$db->Execute('insert into FeatureAssignments (fcID, feID, fdObject) values (?, ?, ?)', array(
			$fc->getFeatureCategoryID(),
			$fe->getFeatureID(),
			serialize($fd)
		));
		// sometimes feature detail objects need to do more with the feature assignments, so we do that here.
		$fa = static::getByID($db->Insert_ID(), $mixed);
		$fd->handleFeatureAssignment($fa);
		return $fa;
	}

	protected function assignmentIsInUse() {
		$categories = FeatureCategory::getList();
		foreach($categories as $cat) {
			if ($cat->assignmentIsInUse($this)) {
				return true;
				break;
			}
		}

		return false;
	}

	public function getFeatureAssignmentID() {return $this->faID;}
	public function getFeatureID() {return $this->feID;}
	public function getFeatureObject() {return Feature::getByID($this->feID);}
	public function getFeatureDetailObject() {return $this->fdObject;}
	public function getFeatureDetailHandle() {
		return $this->feHandle;
	}

	public static function getByID($faID, $mixed) {
		$db = Loader::db();
		$r = $db->GetRow('select faID, fa.fcID, fdObject, fa.feID, fe.feHandle, fc.fcHandle from FeatureAssignments fa inner join FeatureCategories fc on fa.fcID = fc.fcID inner join Features fe on fa.feID = fe.feID where faID = ?', array($faID));
		if (is_array($r) && $r['faID'] == $faID) {
            $class = '\\Concrete\\Core\\Feature\\Assignment\\' . Loader::helper('text')->camelcase($r['fcHandle']) . 'Assignment';
			$fa = Core::make($class);
			$fa->setPropertiesFromArray($r);
			$fa->fdObject = @unserialize($r['fdObject']);
			$fa->loadDetails($mixed);
			return $fa;
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from FeatureAssignments where faID = ?', array($this->getFeatureAssignmentID()));
	}


}
