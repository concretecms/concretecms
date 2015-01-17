<?php
namespace Concrete\Core\Area\Layout;

use Loader;
use \Concrete\Core\Foundation\Object;
use Area;
use Concrete\Core\Block\Block;

abstract class Layout extends Object
{

    /**
     * @var Area
     */
    public $area;

    /**
     * @var Block
     */
    public $block;

    /**
     * @var int
     */
    public $arLayoutID;

    /**
     * @var bool
     */
    public $arLayoutUsesThemeGridFramework;

    /**
     * @var int
     */
    public $arLayoutNumColumns;

    /**
     * @param int $arLayoutID
     * @return CustomLayout|ThemeGridLayout|null
     */
    public static function getByID($arLayoutID)
    {
        $db = Loader::db();
        $row = $db->GetRow('select arLayoutID, arLayoutUsesThemeGridFramework from AreaLayouts where arLayoutID = ?', array($arLayoutID));
        if (is_array($row) && $row['arLayoutID']) {
            if ($row['arLayoutUsesThemeGridFramework']) {
                $al = new ThemeGridLayout();
            } else {
                $al = new CustomLayout();
            }
            $al->setPropertiesFromArray($row);
            $al->arLayoutNumColumns = $db->GetOne('select count(arLayoutColumnID) as totalColumns from AreaLayoutColumns where arLayoutID = ?', array($arLayoutID));
            $al->loadDetails();
            return $al;
        }
    }

    /**
     * @param Area $a
     */
    public function setAreaObject(Area $a)
    {
        $this->area = $a;
    }

    /**
     * @param Block $b
     */
    public function setBlockObject(Block $b)
    {
        $this->block = $b;
    }

    /**
     * @return Block
     */
    public function getBlockObject()
    {
        return $this->block;
    }

    /**
     * @return Area
     */
    public function getAreaObject()
    {
        return $this->area;
    }

    /**
     * @return int
     */
    public function getAreaLayoutID()
    {
        return $this->arLayoutID;
    }

    /**
     * @return bool
     */
    public function isAreaLayoutUsingThemeGridFramework()
    {
        return $this->arLayoutUsesThemeGridFramework;
    }

    /**
     * @return int
     */
    public function getAreaLayoutNumColumns()
    {
        return $this->arLayoutNumColumns;
    }

    /**
     * @return \Concrete\Core\Area\Layout\Column[]
     */
    public function getAreaLayoutColumns()
    {
        $db = Loader::db();
        $r = $db->Execute('select arLayoutColumnID from AreaLayoutColumns where arLayoutID = ? order by arLayoutColumnIndex asc', array($this->arLayoutID));
        $columns = array();
        $class = '\\Concrete\\Core\\Area\\Layout\\' . Loader::helper('text')->camelcase($this->arLayoutType) . 'Column';
        while ($row = $r->FetchRow()) {
            $column = call_user_func_array(array($class, 'getByID'), array($row['arLayoutColumnID']));
            if (is_object($column)) {
                $column->setAreaLayoutObject($this);
                $columns[] = $column;
            }
        }
        return $columns;
    }

    /**
     * @return int
     */
    protected function addLayoutColumn()
    {
        $db = Loader::db();
        $arLayoutColumnDisplayID = $db->GetOne('select max(arLayoutColumnDisplayID) as arLayoutColumnDisplayID from AreaLayoutColumns');
        if ($arLayoutColumnDisplayID) {
            $arLayoutColumnDisplayID++;
        } else {
            $arLayoutColumnDisplayID = 1;
        }
        $index = $db->GetOne('select count(arLayoutColumnID) from AreaLayoutColumns where arLayoutID = ?', array($this->arLayoutID));
        $db->Execute('insert into AreaLayoutColumns (arLayoutID, arLayoutColumnIndex, arLayoutColumnDisplayID) values (?, ?, ?)', array($this->arLayoutID, $index, $arLayoutColumnDisplayID));
        $arLayoutColumnID = $db->Insert_ID();
        return $arLayoutColumnID;
    }

    abstract public function duplicate();
    abstract static public function add();
    abstract public function exportDetails($node);

    /**
     * @param \SimpleXMLElement $node
     */
    public function export($node)
    {
        $layout = $node->addChild('arealayout');
        if ($this->isAreaLayoutUsingThemeGridFramework()) {
            $layout->addAttribute('type', 'theme-grid');
        } else {
            $layout->addAttribute('type', 'custom');
        }
        $this->exportDetails($layout);
        $columns = $layout->addChild('columns');
        foreach ($this->getAreaLayoutColumns() as $column) {
            $column->export($columns);
        }
    }

    public function delete()
    {
        $columns = $this->getAreaLayoutColumns();
        foreach ($columns as $col) {
            $col->delete();
        }
        $db = Loader::db();
        $db->Execute('delete from AreaLayouts where arLayoutID = ?', array($this->arLayoutID));
        $db->Execute('delete from AreaLayoutPresets where arLayoutID = ?', array($this->arLayoutID));
    }

}