<?php

namespace Concrete\Core\Area\Layout;

use Database;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Area\SubArea;
use Page;
use Area;
use RuntimeException;

abstract class Column extends Object implements ColumnInterface
{
    /**
     * @var Layout
     */
    public $arLayout;
    /**
     * @var int
     */
    public $arLayoutColumnIndex;
    /**
     * @var int
     */
    public $arLayoutID;
    /**
     * @var int
     */
    public $arLayoutColumnID;
    /**
     * @var int
     */
    public $arLayoutColumnDisplayID;
    /**
     * @var int
     */
    public $arID;

    /**
     * @param int $arLayoutColumnID
     *
     * @abstract
     */
    public static function getByID($arLayoutColumnID)
    {
        throw new RuntimeException('This method has not yet been implemented.');
    }

    abstract public function exportDetails($node);
    abstract public function getAreaLayoutColumnClass();

    /**
     * @param int $arLayoutColumnID
     */
    protected function loadBasicInformation($arLayoutColumnID)
    {
        $db = Database::connection();
        $row = $db->GetRow('select * from AreaLayoutColumns where arLayoutColumnID = ?', array($arLayoutColumnID));
        if (is_array($row) && $row['arLayoutColumnID']) {
            $this->setPropertiesFromArray($row);
        }
    }

    /**
     * @param Layout $arLayout
     */
    public function setAreaLayoutObject($arLayout)
    {
        $this->arLayout = $arLayout;
    }

    /**
     * @return Layout
     */
    public function getAreaLayoutObject()
    {
        return $this->arLayout;
    }

    /**
     * @return int
     */
    public function getAreaLayoutColumnIndex()
    {
        return $this->arLayoutColumnIndex;
    }

    /**
     * @return int
     */
    public function getAreaLayoutID()
    {
        return $this->arLayoutID;
    }

    /**
     * @return int
     */
    public function getAreaID()
    {
        return $this->arID;
    }

    /**
     * @param \SimpleXMLElement $node
     */
    public function export($node)
    {
        $column = $node->addChild('column');
        $this->exportDetails($column);
        $area = $this->getAreaObject();
        $area->export($column, $area->getAreaCollectionObject());
    }

    /**
     * @param Column $newAreaLayout
     *
     * @return int
     */
    public function duplicate($newAreaLayout)
    {
        $db = Database::connection();
        $v = array($newAreaLayout->getAreaLayoutID(), $this->arLayoutColumnIndex, $this->arLayoutColumnDisplayID);
        $db->executeQuery('insert into AreaLayoutColumns (arLayoutID, arLayoutColumnIndex, arLayoutColumnDisplayID) values (?, ?, ?)', $v);
        $newAreaLayoutColumnID = $db->Insert_ID();

        return $newAreaLayoutColumnID;
    }

    /**
     * @return Area|null
     */
    public function getAreaObject()
    {
        $db = Database::connection();
        $row = $db->GetRow('select cID, arHandle from Areas where arID = ?', array($this->arID));
        if ($row['cID'] && $row['arHandle']) {
            $c = Page::getByID($row['cID']);
            $area = Area::get($c, $row['arHandle']);

            return $area;
        }
    }

    /**
     * @return int
     */
    public function getAreaLayoutColumnID()
    {
        return $this->arLayoutColumnID;
    }

    /**
     * unique but doesn't change between version edits on a given page.
     *
     * @return int
     */
    public function getAreaLayoutColumnDisplayID()
    {
        return $this->arLayoutColumnDisplayID;
    }

    /**
     * @return string
     */
    public function getContents($disableControls = false)
    {
        ob_start();
        $this->display($disableControls);
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    protected function getSubAreaMaximumColumns()
    {
        return 0;
    }

    /**
     * @param bool $disableControls
     */
    public function display($disableControls = false)
    {
        $layout = $this->getAreaLayoutObject();
        $a = $layout->getAreaObject();
        if (is_object($a)) {
            $as = new SubArea($this->getAreaLayoutColumnDisplayID(), $a->getAreaHandle(), $a->getAreaID());
            $as->setAreaGridMaximumColumns($this->getSubAreaMaximumColumns());
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
    }

    /**
     * @param int $arID
     */
    public function setAreaID($arID)
    {
        $db = Database::connection();
        $this->arID = $arID;
        $db->executeQuery('update AreaLayoutColumns set arID = ? where arLayoutColumnID = ?', array($arID, $this->arLayoutColumnID));
    }

    public function delete()
    {
        $db = Database::connection();
        $db->executeQuery("delete from AreaLayoutColumns where arLayoutColumnID = ?", array($this->arLayoutColumnID));

        // now we check to see if this area id is in use anywhere else. If it isn't we delete the sub area.
        $r = $db->fetchColumn('select count(arLayoutColumnID) from AreaLayoutColumns where arID = ?', array($this->arID));
        if ($r < 1) {
            $area = $this->getAreaObject();
            if ($area instanceof SubArea) {
                $area->delete();
            }
        }
    }
}
