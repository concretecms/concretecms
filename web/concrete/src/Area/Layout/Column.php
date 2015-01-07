<?php
namespace Concrete\Core\Area\Layout;
use Loader;
use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Area\SubArea;
use Page, Area;
abstract class Column extends Object {

	abstract static public function getByID($arLayoutColumnID);
	abstract public function getAreaLayoutColumnClass();
    abstract public function exportDetails($node);

	protected function loadBasicInformation($arLayoutColumnID) {
		$db = Loader::db();
		$row = $db->GetRow('select * from AreaLayoutColumns where arLayoutColumnID = ?', array($arLayoutColumnID));
		if (is_array($row) && $row['arLayoutColumnID']) {
			$this->setPropertiesFromArray($row);
		}
	}

	public function setAreaLayoutObject($arLayout) {
		$this->arLayout = $arLayout;
	}

	public function getAreaLayoutObject() {
		return $this->arLayout;
	}

	public function getAreaLayoutColumnIndex() {
		return $this->arLayoutColumnIndex;
	}
		
	public function getAreaLayoutID() {
		return $this->arLayoutID;
	}

	public function getAreaID() {
		return $this->arID;
	}

    public function export($node)
    {
        $column = $node->addChild('column');
        $this->exportDetails($column);
        $area = $this->getAreaObject();
        $area->export($column, $area->getAreaCollectionObject());
    }

	protected function duplicate($newAreaLayout) {
		$db = Loader::db();
		$v = array($newAreaLayout->getAreaLayoutID(), $this->arLayoutColumnIndex, $this->arLayoutColumnDisplayID);
		$db->Execute('insert into AreaLayoutColumns (arLayoutID, arLayoutColumnIndex, arLayoutColumnDisplayID) values (?, ?, ?)', $v);
		$newAreaLayoutColumnID = $db->Insert_ID();
		return $newAreaLayoutColumnID;
	}

	public function getAreaObject() {
		$db = Loader::db();
		$row = $db->GetRow('select cID, arHandle from Areas where arID = ?', array($this->arID));
		if ($row['cID'] && $row['arHandle']) {
			$c = Page::getByID($row['cID']);
			$area = Area::get($c, $row['arHandle']);
			return $area;
		}
	}

	public function getAreaLayoutColumnID() {
		return $this->arLayoutColumnID;
	}
	
	// unique but doesn't change between version edits on a given page.
	public function getAreaLayoutColumnDisplayID() {
		return $this->arLayoutColumnDisplayID;
	}

	public function display($disableControls = false) {
		$layout = $this->getAreaLayoutObject();
		$a = $layout->getAreaObject();
		$as = new SubArea($this->getAreaLayoutColumnDisplayID(), $a->getAreaHandle(), $a->getAreaID());
		$as->setAreaDisplayName(t('Column %s', $this->getAreaLayoutColumnIndex() + 1));
		if ($disableControls) {
			$as->disableControls();
		}
		$c = $a->getAreaCollectionObject();
		$as->load($c);
		if (!$this->getAreaID()) {
            $this->setAreaID($as->getAreaID());
		}
        $as->setSubAreaBlockObject($this->arLayout->getBlockObject());
		$as->display($c);
	}

    public function setAreaID($arID)
    {
        $db = Loader::db();
        $this->arID = $arID;
        $db->Execute('update AreaLayoutColumns set arID = ? where arLayoutColumnID = ?', array($arID, $this->arLayoutColumnID));
    }

	public function delete() {
		$db = Loader::db();
        $db->Execute("delete from AreaLayoutColumns where arLayoutColumnID = ?", array($this->arLayoutColumnID));

        // now we check to see if this area id is in use anywhere else. If it isn't we delete the sub area.
        $r = $db->GetOne('select count(arLayoutColumnID) from AreaLayoutColumns where arID = ?', array($this->arID));
        if ($r < 1) {
            $area = $this->getAreaObject();
            if ($area instanceof SubArea) {
                $area->delete();
            }
        }
	}

}