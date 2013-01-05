<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_SubArea extends Area {

	public function create($c, $arHandle) {
		$db = Loader::db();
		$db->Replace('Areas', array('cID' => $c->getCollectionID(), 'arHandle' => $arHandle, 'arParentID' => $this->arParentID), array('arHandle', 'cID'), true);
		$area = self::get($c, $arHandle);
		$area->rescanAreaPermissionsChain();
		return $area;
	}

	public function __construct($arHandle, Area $parent) {
		$arHandle = $parent->getAreaHandle() . ' : ' . $arHandle;
		$this->arParentID = $parent->getAreaID();
		parent::__construct($arHandle);
	}	



}