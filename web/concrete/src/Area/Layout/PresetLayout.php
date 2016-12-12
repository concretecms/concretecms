<?php
namespace Concrete\Core\Area\Layout;

use Concrete\Core\Area\Layout\Preset\PresetInterface;
use Loader;
use Core;

class PresetLayout extends Layout
{

    /**
     * @var string
     */
    protected $arLayoutType = 'preset';

    /**
     * @var int
     */
    public $arLayoutID;

    /**
     * @var bool
     */
    public $arLayoutIsPreset = true;

    /**
     * @var string
     */
    protected $preset;

    protected $presetObject;

    protected function loadDetails()
    {
        $db = Loader::db();
        $row = $db->GetRow('select preset from AreaLayoutsUsingPresets where arLayoutID = ?', array($this->arLayoutID));
        $this->setPropertiesFromArray($row);
    }

    public function getPresetObject()
    {
        if (!isset($this->presetObject)) {
            $manager = Core::make('manager/area_layout_preset_provider');
            $this->presetObject = $manager->getPresetByIdentifier($this->preset);
        }
        return $this->presetObject;
    }

    protected function loadColumnNumber()
    {
        $preset = $this->getPresetObject();
        if (is_object($preset)) {
            $this->arLayoutNumColumns = count($preset->getColumns());
        }
    }

    /**
     * @return string
     */
    public function getAreaLayoutPresetHandle()
    {
        return $this->preset;
    }

    /**
     * @param \SimpleXMLElement $node
     */
    public function exportDetails($node)
    {
        $node->addAttribute('preset', $this->preset);
    }

    /**
     * @return PresetLayout
     */
    public function duplicate()
    {

        $db = Loader::db();
        $v = array($this->arLayoutIsPreset);
        $db->Execute('insert into AreaLayouts (arLayoutIsPreset) values (?)', $v);
        $newAreaLayoutID = $db->Insert_ID();
        if ($newAreaLayoutID) {
            $v = array($newAreaLayoutID, $this->getAreaLayoutPresetHandle());
            $db->Execute('insert into AreaLayoutsUsingPresets (arLayoutID, preset) values (?, ?)', $v);
            $newAreaLayout = Layout::getByID($newAreaLayoutID);

            $columns = $this->getAreaLayoutColumns();
            foreach ($columns as $col) {
                $col->duplicate($newAreaLayout);
            }

            return $newAreaLayout;
        }
    }

    /**
     * @param PresetInterface $preset
     * @return PresetLayout
     */
    public static function add(PresetInterface $preset)
    {
        $db = Loader::db();
        $db->Execute('insert into AreaLayouts (arLayoutIsPreset) values (?)', array(1));
        $arLayoutID = $db->Insert_ID();
        if ($arLayoutID) {
            $db->Execute('insert into AreaLayoutsUsingPresets (arLayoutID, preset) values (?, ?)', array($arLayoutID, $preset->getIdentifier()));
            $ar = static::getByID($arLayoutID);
            return $ar;
        }
    }


}