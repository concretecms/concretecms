<?php
namespace Concrete\Core\Area;
use Loader;
use Page;
use Permissions;
use Stack;
class GlobalArea extends Area {

	public function isGlobalArea() {return true;}

	public function create($c, $arHandle) {
		$db = Loader::db();
		Stack::getOrCreateGlobalArea($arHandle);
		$db->Replace('Areas', array('cID' => $c->getCollectionID(), 'arHandle' => $arHandle, 'arIsGlobal' => 1), array('arHandle', 'cID'), true);
        $this->refreshCache($c);
		$area = self::get($c, $arHandle);
		$area->rescanAreaPermissionsChain();
		return $area;
	}

	public function getAreaDisplayName() {
		return t('Sitewide %s', $this->getAreaHandle());
	}

	public function getTotalBlocksInArea() {
		$stack = $this->getGlobalAreaStackObject();
		$ax = Area::get($stack, STACKS_AREA_NAME);
        if (is_object($ax)) {
    		return $ax->getTotalBlocksInArea();
	    }
        return 0;
    }

	protected function getGlobalAreaStackObject() {
		$c = Page::getCurrentPage();
		$cp = new Permissions($c);
		if ($cp->canViewPageVersions()) {
			$stack = Stack::getByName($this->arHandle);
		} else {
			$stack = Stack::getByName($this->arHandle, 'ACTIVE');
		}
		return $stack;
	}

	public function getTotalBlocksInAreaEditMode() {
		$stack = $this->getGlobalAreaStackObject();
		$ax = Area::get($stack, STACKS_AREA_NAME);

		$db = Loader::db();
		$r = $db->GetOne('select count(b.bID) from CollectionVersionBlocks cvb inner join Blocks b on cvb.bID = b.bID inner join BlockTypes bt on b.btID = bt.btID where cID = ? and cvID = ? and arHandle = ?',
			array($stack->getCollectionID(), $stack->getVersionID(), $ax->getAreaHandle()));
		return $r;
	}

	public function getAreaBlocks() {
		$cp = new Permissions($this->c);
		if ($cp->canViewPageVersions()) {
			$stack = Stack::getByName($this->arHandle);
		} else {
			$stack = Stack::getByName($this->arHandle, 'ACTIVE');
		}
        $blocksTmp = array();
		if (is_object($stack)) {
			$blocksTmp = $stack->getBlocks(STACKS_AREA_NAME);
			$globalArea = self::get($stack, STACKS_AREA_NAME);
		}

		$blocks = array();
		foreach($blocksTmp as $ab) {
			$ab->setBlockAreaObject($globalArea);
			$ab->setBlockActionCollectionID($stack->getCollectionID());
			$blocks[] = $ab;
		}

		unset($blocksTmp);
		return $blocks;
	}

	public function display() {
		$c = Page::getCurrentPage();
		parent::display($c);
	}

	/**
	 * Note that this function does not delete the global area's stack.
	 * You probably want to call the "delete" method of the Stack model instead.
	 */
	public static function deleteByName($arHandle) {
		$db = Loader::db();
		$r = $db->Execute('select cID from Areas where arHandle = ? and arIsGlobal = 1', array($arHandle));
		$db->Execute('delete from Areas where arHandle = ? and arIsGlobal = 1', array($arHandle));
	}



}