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

	public function getSubAreaParentPermissionsObject() {
		$db = Loader::db();
		$arParentID = $this->arParentID;
		if ($arParentID == 0) {
			return false;
		}

		while ($arParentID > 0) {
			$row = $db->GetRow('select arID, arHandle, arParentID, arOverrideCollectionPermissions from Areas where arID = ?', array($arParentID));
			$arParentID = $row['arParentID'];
			if ($row['arOverrideCollectionPermissions']) {
				break;
			}
		}

		$a = Area::get($this->c, $row['arHandle']);
		return $a;
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