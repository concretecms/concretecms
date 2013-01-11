<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_SubArea extends Area {

	const AREA_SUB_DELIMITER = ' : ';

	public function create($c, $arHandle) {
		$db = Loader::db();
		$db->Replace('Areas', array('cID' => $c->getCollectionID(), 'arHandle' => $arHandle, 'arParentID' => $this->arParentID), array('arHandle', 'cID'), true);
		$area = self::get($c, $arHandle);
		$area->rescanAreaPermissionsChain();
		return $area;
	}

	public function __construct($arHandle, Area $parent) {
		$arHandle = $parent->getAreaHandle() . self::AREA_SUB_DELIMITER . $arHandle;
		$this->arParentID = $parent->getAreaID();
		parent::__construct($arHandle);
	}	


	public function delete() {
		$db = Loader::db();
		$blocks = $this->getAreaBlocksArray();
		foreach($blocks as $b) {
			$bp = new Permissions($b);
			if ($bp->canDeleteBlock()) {
				$b->deleteBlock();
			}
		}
		$db->Execute('delete from Areas where arID = ?', array($this->arID));
	}
}