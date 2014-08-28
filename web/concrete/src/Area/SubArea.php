<?php
namespace Concrete\Core\Area;
use Loader;
use Block;
use Page;

class SubArea extends Area {

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

	public function getSubAreaBlockObject() {
		$db = Loader::db();
		$bID = $db->GetOne('select cvb.bID from btCoreAreaLayout bta inner join AreaLayoutColumns alc on bta.arLayoutID = alc.arLayoutID inner join CollectionVersionBlocks cvb on bta.bID = cvb.bID where cvb.cID = ? and cvb.cvID = ? and alc.arID = ?', array($this->c->getCollectionID(), $this->c->getVersionID(), $this->arID));
		$arHandle = $db->GetOne('select arHandle from Areas where arID = ?', array($this->arParentID));
		if ($bID) {
			$b = Block::getByID($bID, $this->c, $arHandle);
			return $b;
		}
	}

	public function __construct($arHandle, Area $parent) {
		$arHandle = $parent->getAreaHandle() . self::AREA_SUB_DELIMITER . $arHandle;
		$this->arParentID = $parent->getAreaID();
		parent::__construct($arHandle);
	}

    public function getAreaParentID()
    {
        return $this->arParentID;
    }

    public function export($p, $page)
    {
        $c = $this->getAreaCollectionObject();
        $style = $c->getAreaCustomStyle($this);
        if (is_object($style)) {
            $set = $style->getStyleSet();
            $set->export($p);
        }
        $blocks = $page->getBlocks($this->getAreaHandle());
        foreach ($blocks as $bl) {
            $bl->export($p);
        }
    }

	public function delete() {
		$db = Loader::db();
		$blocks = $this->getAreaBlocksArray();
		foreach($blocks as $b) {
			$bp = new \Permissions($b);
			if ($bp->canDeleteBlock()) {
				$b->deleteBlock();
			}
		}
		$db->Execute('delete from Areas where arID = ?', array($this->arID));
	}
}