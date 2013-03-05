<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Model_FeatureAssignment extends Object {

	abstract public function loadDetails();
	abstract public static function getList($mixed);
	
	public static function add(FeatureCategory $fc, FeatureDetail $fd) {
		$db = Loader::db();
		$db->Execute('insert into FeatureAssignments (fcID, fdObject) values (?, ?)', array(
			$fc->getFeatureCategoryID(),
			serialize($fd)
		));
		return FeatureAssignment::getByID($db->Insert_ID());
	}

	public function getFeatureAssignmentID() {return $this->faID;}
	public function getFeatureDetailObject() {return $this->fdObject;}
	public function getFeatureDetailHandle() {
		$o = $this->getFeatureDetailObject();
		$handle = substr(get_class($o), 0, strpos(get_class($o), 'FeatureDetail'));
		return Loader::helper('text')->uncamelcase($handle);
	}
	
	public static function getByID($faID) {
		$db = Loader::db();
		$r = $db->GetRow('select faID, fa.fcID, fdObject, fc.fcHandle from FeatureAssignments fa inner join FeatureCategories fc on fa.fcID = fc.fcID where faID = ?', array($faID));
		if (is_array($r) && $r['faID'] == $faID) {
			$class = Loader::helper('text')->camelcase($r['fcHandle']) . 'FeatureAssignment';
			$fa = new $class();
			$fa->setPropertiesFromArray($r);
			$fa->fdObject = @unserialize($r['fdObject']);
			$fa->loadDetails();
			return $fa;
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from FeatureAssignments where faID = ?', array($this->getFeatureAssignmentID()));
	}

		
}
