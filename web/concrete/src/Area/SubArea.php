<?php
namespace Concrete\Core\Area;
use Loader;
use Block;
use Page;

class SubArea extends Area {

	const AREA_SUB_DELIMITER = ' : ';

    protected $parentBlock;

    public function setSubAreaBlockObject($block)
    {
        $this->parentBlock = $block;
    }

	public function create($c, $arHandle) {
		$db = Loader::db();
		$db->Replace('Areas', array('cID' => $c->getCollectionID(), 'arHandle' => $arHandle, 'arParentID' => $this->arParentID), array('arHandle', 'cID'), true);
        $this->refreshCache($c);
		$area = self::get($c, $arHandle);
		$area->rescanAreaPermissionsChain();
		return $area;
	}

	public function getSubAreaParentPermissionsObject()
    {
		$cache = \Core::make('cache/request');
        $item = $cache->getItem(sprintf('subarea/parent/permissions/%s', $this->getAreaID()));
        if (!$item->isMiss()) {
            return $item->get();
        }

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
        $item->set($a);
		return $a;
	}

	public function getSubAreaBlockObject() {
        return $this->parentBlock;
	}

	public function __construct($arHandle, $arParentHandle, $arParentID) {
        $this->arParentID = $arParentID;
		$arHandle = $arParentHandle . self::AREA_SUB_DELIMITER . $arHandle;
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