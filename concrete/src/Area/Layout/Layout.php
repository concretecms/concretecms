<?php

namespace Concrete\Core\Area\Layout;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Support\Facade\Application;
use Core;
use Database;

abstract class Layout extends ConcreteObject
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
     *
     * @return \Concrete\Core\Area\Layout\PresetLayout|CustomLayout|ThemeGridLayout|null
     */
    public static function getByID($arLayoutID)
    {
        $app = Application::getFacadeApplication();
        /** @var RequestCache $cache */
        $cache = $app->make('cache/request');
        $key = '/Area/Layout/' . $arLayoutID;
        if ($cache->isEnabled()) {
            $item = $cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $al = null;
        $db = Database::connection();
        $row = $db->GetRow('select arLayoutID, arLayoutIsPreset, arLayoutUsesThemeGridFramework from AreaLayouts where arLayoutID = ?', [$arLayoutID]);
        if (is_array($row) && $row['arLayoutID']) {
            if ($row['arLayoutUsesThemeGridFramework']) {
                $al = new ThemeGridLayout();
            } elseif ($row['arLayoutIsPreset']) {
                $al = new PresetLayout();
            } else {
                $al = new CustomLayout();
            }
            $al->setPropertiesFromArray($row);
            $al->loadDetails();
            $al->loadColumnNumber();
        }

        if (isset($item) && $item->isMiss()) {
            $item->set($al);
            $cache->save($item);
        }

        return $al;
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
        $db = Database::connection();
        $r = $db->Execute('select arLayoutColumnID from AreaLayoutColumns where arLayoutID = ? order by arLayoutColumnIndex asc', [$this->arLayoutID]);
        $columns = [];
        $class = '\\Concrete\\Core\\Area\\Layout\\' . Core::make('helper/text')->camelcase($this->arLayoutType) . 'Column';
        while ($row = $r->fetch()) {
            $column = call_user_func_array([$class, 'getByID'], [$row['arLayoutColumnID']]);
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
    public function addLayoutColumn()
    {
        $db = Database::connection();
        $arLayoutColumnDisplayID = $db->GetOne('select max(arLayoutColumnDisplayID) as arLayoutColumnDisplayID from AreaLayoutColumns');
        if ($arLayoutColumnDisplayID) {
            $arLayoutColumnDisplayID++;
        } else {
            $arLayoutColumnDisplayID = 1;
        }
        $index = $db->GetOne('select count(arLayoutColumnID) from AreaLayoutColumns where arLayoutID = ?', [$this->arLayoutID]);
        $db->Execute('insert into AreaLayoutColumns (arLayoutID, arLayoutColumnIndex, arLayoutColumnDisplayID) values (?, ?, ?)', [$this->arLayoutID, $index, $arLayoutColumnDisplayID]);

        return $db->Insert_ID();
    }

    abstract public function duplicate();

    abstract public function exportDetails($node);

    /**
     * @param \SimpleXMLElement $node
     */
    public function export($node)
    {
        $layout = $node->addChild('arealayout');
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
        $db = Database::connection();
        $db->Execute('delete from AreaLayouts where arLayoutID = ?', [$this->arLayoutID]);
        $db->Execute('delete from AreaLayoutPresets where arLayoutID = ?', [$this->arLayoutID]);
    }

    /**
     * @return \Concrete\Core\Area\Layout\Formatter\FormatterInterface
     */
    public function getFormatter()
    {
        $class = '\\Concrete\\Core\\Area\\Layout\\' .
            'Formatter\\' . Core::make('helper/text')->camelcase($this->arLayoutType) . 'Formatter';

        return new $class($this);
    }

    protected function loadColumnNumber()
    {
        $db = Database::connection();
        $this->arLayoutNumColumns = $db->GetOne('select count(arLayoutColumnID) as totalColumns from AreaLayoutColumns where arLayoutID = ?', [$this->arLayoutID]);
    }
}
