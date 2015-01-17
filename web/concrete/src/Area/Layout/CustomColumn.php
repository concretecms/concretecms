<?php
namespace Concrete\Core\Area\Layout;

use Loader;

class CustomColumn extends Column
{

    /**
     * @var int;
     */
    public $arLayoutColumnWidth;

    /**
     * @param int $arLayoutColumnID
     * @return static
     */
    public static function getByID($arLayoutColumnID)
    {
        $db = Loader::db();
        $row = $db->GetRow('select * from AreaLayoutCustomColumns where arLayoutColumnID = ?', array($arLayoutColumnID));
        if (is_array($row) && $row['arLayoutColumnID']) {
            $al = new static();
            $al->loadBasicInformation($arLayoutColumnID);
            $al->setPropertiesFromArray($row);
            return $al;
        }
    }

    /**
     * @param Column $newAreaLayout
     * @return CustomColumn
     */
    public function duplicate($newAreaLayout)
    {
        $areaLayoutColumnID = parent::duplicate($newAreaLayout);
        $db = Loader::db();
        $v = array($areaLayoutColumnID, $this->arLayoutColumnWidth);
        $db->Execute('insert into AreaLayoutCustomColumns (arLayoutColumnID, arLayoutColumnWidth) values (?, ?)', $v);
        $newAreaLayoutColumn = CustomColumn::getByID($areaLayoutColumnID);
        return $newAreaLayoutColumn;
    }

    /**
     * @param \SimpleXMLElement $node
     */
    public function exportDetails($node)
    {
        $node->addAttribute('width', $this->arLayoutColumnWidth);
    }

    /**
     * @return string
     */
    public function getAreaLayoutColumnClass()
    {
        return 'ccm-layout-column';
    }

    /**
     * @return int
     */
    public function getAreaLayoutColumnWidth()
    {
        return $this->arLayoutColumnWidth;
    }

    /**
     * @param int $width
     */
    public function setAreaLayoutColumnWidth($width)
    {
        $this->arLayoutColumnWidth = $width;
        $db = Loader::db();
        $db->Execute('update AreaLayoutCustomColumns set arLayoutColumnWidth = ? where arLayoutColumnID = ?', array($width, $this->arLayoutColumnID));
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute("delete from AreaLayoutCustomColumns where arLayoutColumnID = ?", array($this->arLayoutColumnID));
        parent::delete();
    }

}