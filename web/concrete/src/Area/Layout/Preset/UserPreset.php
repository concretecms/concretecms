<?php
namespace Concrete\Core\Area\Layout\Preset;

use Concrete\Core\Area\Layout\Layout;
use Concrete\Core\Area\Layout\Preset\Formatter\UserFormatter;
use Loader;
use \Concrete\Core\Foundation\Object;

class UserPreset extends Object
{

    /**
     * @var int
     */
    public $arLayoutPresetID;

    /**
     * @var string
     */
    public $arLayoutPresetName;

    /**
     * @var int
     */
    public $arLayoutID;

    /**
     * @param Layout $arLayout
     * @param string $name
     * @return Preset
     */
    public static function add(Layout $arLayout, $name)
    {
        $db = Loader::db();
        $db->Execute(
            'insert into AreaLayoutPresets (arLayoutID, arLayoutPresetName) values (?, ?)',
            array(
                $arLayout->getAreaLayoutID(),
                $name
            )
        );
        return static::getByID($db->Insert_ID());
    }

    /**
     * @return Preset[]
     */
    public static function getList()
    {
        $db = Loader::db();
        $r = $db->Execute('select arLayoutPresetID from AreaLayoutPresets order by arLayoutPresetName asc');
        $presets = array();
        while ($row = $r->FetchRow()) {
            $preset = static::getByID($row['arLayoutPresetID']);
            if (is_object($preset)) {
                $presets[] = $preset;
            }
        }
        return $presets;
    }

    /**
     * @param int $arLayoutPresetID
     * @return static
     */
    public static function getByID($arLayoutPresetID)
    {
        $db = Loader::db();
        $row = $db->GetRow(
            'select arLayoutID, arLayoutPresetID, arLayoutPresetName from AreaLayoutPresets where arLayoutPresetID = ?',
            array(
                $arLayoutPresetID
            )
        );
        if (is_array($row) && $row['arLayoutPresetID']) {
            $preset = new static();
            $preset->setPropertiesFromArray($row);
            return $preset;
        }
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute(
            'delete from AreaLayoutPresets where arLayoutPresetID = ?',
            array(
                $this->arLayoutPresetID
            )
        );
    }


    /**
     * @return int
     */
    public function getAreaLayoutPresetID()
    {
        return $this->arLayoutPresetID;
    }

    /**
     * @return string
     */
    public function getAreaLayoutPresetName()
    {
        return $this->arLayoutPresetName;
    }

    /**
     * @return int
     */
    public function getAreaLayoutID()
    {
        return $this->arLayoutID;
    }

    /**
     * @return CustomLayout|ThemeGridLayout|null
     */
    public function getAreaLayoutObject()
    {
        return Layout::getByID($this->arLayoutID);
    }

    /**
     * @param Layout $arLayout
     */
    public function updateAreaLayoutObject(Layout $arLayout)
    {
        $db = Loader::db();
        $db->Execute(
            'update AreaLayoutPresets set arLayoutID = ? where arLayoutPresetID = ?',
            array(
                $arLayout->getAreaLayoutID(),
                $this->arLayoutPresetID
            )
        );
        $this->arLayoutID = $arLayout->getAreaLayoutID();
    }

    /**
     * @param string $arLayoutPresetName
     */
    public function updateName($arLayoutPresetName)
    {
        $db = Loader::db();
        $db->Execute(
            'update AreaLayoutPresets set arLayoutPresetName = ? where arLayoutPresetID = ?',
            array(
                $arLayoutPresetName,
                $this->arLayoutPresetID
            )
        );
        $this->arLayoutPresetName = $arLayoutPresetName;
    }

    public function getPresetObject()
    {
        $formatter = new UserFormatter($this->getAreaLayoutObject());
        $columns = $this->getAreaLayoutObject()->getAreaLayoutColumns();
        $presetColumns = array();
        foreach($columns as $column) {
            $presetColumns[] = new Column($column->getColumnHtmlObject());
        }
        $p = new Preset($this->arLayoutID, $this->getAreaLayoutPresetName(),
            $formatter, $presetColumns);
        return $p;
    }

}