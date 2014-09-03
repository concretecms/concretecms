<?php
namespace Concrete\Core\Area\Layout;

use Loader;
use \Concrete\Core\Foundation\Object;

class Preset extends Object
{

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


    public function getAreaLayoutPresetID()
    {
        return $this->arLayoutPresetID;
    }

    public function getAreaLayoutPresetName()
    {
        return $this->arLayoutPresetName;
    }

    public function getAreaLayoutID()
    {
        return $this->arLayoutID;
    }

    public function getAreaLayoutObject()
    {
        return Layout::getByID($this->arLayoutID);
    }

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


}