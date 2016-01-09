<?php
namespace Concrete\Core\Area\Layout;

use Loader;

class CustomLayout extends Layout
{

    /**
     * @var string
     */
    protected $arLayoutType = 'custom';

    /**
     * @var int
     */
    public $arLayoutSpacing;

    /**
     * @var int
     */
    public $arLayoutID;

    /**
     * @var bool
     */
    public $arLayoutIsCustom;

    protected function loadDetails()
    {
        $db = Loader::db();
        $row = $db->GetRow('select arLayoutSpacing, arLayoutIsCustom from AreaLayouts where arLayoutID = ?', array($this->arLayoutID));
        $this->setPropertiesFromArray($row);
    }

    /**
     * @return int
     */
    public function getAreaLayoutSpacing()
    {
        return $this->arLayoutSpacing;
    }

    /**
     * @return bool
     */
    public function hasAreaLayoutCustomColumnWidths()
    {
        return $this->arLayoutIsCustom;
    }

    /**
     * @param \SimpleXMLElement $node
     */
    public function exportDetails($node)
    {
        $node->addAttribute('spacing', $this->arLayoutSpacing);
        $node->addAttribute('custom-widths', $this->arLayoutIsCustom);
    }

    /**
     * @return CustomLayout|ThemeGridLayout
     */
    public function duplicate()
    {
        $db = Loader::db();
        $v = array($this->arLayoutSpacing, $this->arLayoutIsCustom);
        $db->Execute('insert into AreaLayouts (arLayoutSpacing, arLayoutIsCustom) values (?, ?)', $v);
        $newAreaLayoutID = $db->Insert_ID();
        if ($newAreaLayoutID) {
            $newAreaLayout = Layout::getByID($newAreaLayoutID);
            $columns = $this->getAreaLayoutColumns();
            foreach ($columns as $col) {
                $col->duplicate($newAreaLayout);
            }
            return $newAreaLayout;
        }
    }

    /**
     * @param int $spacing
     */
    public function setAreaLayoutColumnSpacing($spacing)
    {
        if (!$spacing) {
            $spacing = 0;
        }
        $db = Loader::db();
        $db->Execute('update AreaLayouts set arLayoutSpacing = ? where arLayoutID = ?', array($spacing, $this->arLayoutID));
        $this->arLayoutSpacing = $spacing;
    }

    /**
     * Enable custom column widths on layouts
     */
    public function enableAreaLayoutCustomColumnWidths()
    {
        $db = Loader::db();
        $db->Execute('update AreaLayouts set arLayoutIsCustom = ? where arLayoutID = ?', array(1, $this->arLayoutID));
        $this->arLayoutIsCustom = true;
    }

    /**
     * Disable custom column widths on layouts
     */
    public function disableAreaLayoutCustomColumnWidths()
    {
        $db = Loader::db();
        $db->Execute('update AreaLayouts set arLayoutIsCustom = ? where arLayoutID = ?', array(0, $this->arLayoutID));
        $this->arLayoutIsCustom = false;
    }


    /**
     * @param int $spacing
     * @param bool $iscustom
     * @return CustomLayout|ThemeGridLayout|null
     */
    public static function add($spacing = 0, $iscustom = false)
    {
        if (!$spacing) {
            $spacing = 0; // just in case
        }
        if (!$iscustom) {
            $iscustom = 0;
        } else {
            $iscustom = 1;
        }

        $db = Loader::db();
        $db->Execute('insert into AreaLayouts (arLayoutSpacing, arLayoutIsCustom, arLayoutUsesThemeGridFramework) values (?, ?, ?)', array($spacing, $iscustom, 0));
        $arLayoutID = $db->Insert_ID();
        if ($arLayoutID) {
            $ar = static::getByID($arLayoutID);
            return $ar;
        }
    }

    /**
     * @return static
     */
    public function addLayoutColumn()
    {
        $columnID = parent::addLayoutColumn();
        $db = Loader::db();
        $db->Execute('insert into AreaLayoutCustomColumns (arLayoutColumnID, arLayoutColumnWidth) values (?, 0)', array($columnID));
        return CustomColumn::getByID($columnID);
    }

}