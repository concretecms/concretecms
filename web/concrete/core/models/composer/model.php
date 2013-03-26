<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_Composer extends Object {

	public function getComposerID() {return $this->cmpID;}
	
	public static function add($cmpName, CollectionType $ct) {
		$db = Loader::db();
		$db->Execute('insert into Composers (cmpName, ctID) values (?, ?)', array(
			$cmpName, $ct->getCollectionTypeID()
		));
		return Composer::getByID($db->Insert_ID());
	}

	public static function getByID($cmpID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from Composers where cmpID = ?', array($cmpID));
		if (is_array($r) && $r['cmpID']) {
			$cm = new Composer;
			$cm->setPropertiesFromArray($r);
			return $cm;
		}
	}

	public function setConfiguredComposerTargetObject(ComposerTargetConfiguration $configuredTarget) {
		$db = Loader::db();
		if (is_object($configuredTarget)) {
			$db->Execute('update Composers set cmpTargetObject = ? where cmpID = ?', array(
				@serialize($configuredTarget),
				$this->getComposerID()
			));
		}
	}

}