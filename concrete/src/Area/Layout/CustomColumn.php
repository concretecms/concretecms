<?php
namespace Concrete\Core\Area\Layout;

use HtmlObject\Element;
use Loader;

class CustomColumn extends Column
{
    /**
     * @var int;
     */
    public $arLayoutColumnWidth;

    /**
     * @param int $arLayoutColumnID
     *
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
     *
     * @return CustomColumn
     */
    public function duplicate($newAreaLayout)
    {
        $areaLayoutColumnID = parent::duplicate($newAreaLayout);
        $db = Loader::db();
        $v = array($areaLayoutColumnID, $this->arLayoutColumnWidth);
        $db->Execute('insert into AreaLayoutCustomColumns (arLayoutColumnID, arLayoutColumnWidth) values (?, ?)', $v);
        $newAreaLayoutColumn = self::getByID($areaLayoutColumnID);

        return $newAreaLayoutColumn;
    }

    /**
     * @param \SimpleXMLElement $node
     */
    public function exportDetails($node)
    {
        $node->addAttribute('width', $this->arLayoutColumnWidth);
    }

    public function getAreaLayoutColumnClass()
    {
        return 'ccm-layout-column';
    }

    protected function getColumnElement($contents)
    {
        $element = new Element('div');
        $element->addClass($this->getAreaLayoutColumnClass())->id('ccm-layout-column-'.$this->arLayoutColumnID);
        $inner = new Element('div');
        $inner->addClass('ccm-layout-column-inner');
        $inner->setValue($contents);
        $element->appendChild($inner);

        return $element;
    }

    public function getColumnHtmlObject()
    {
        $contents = $this->getContents();

        return $this->getColumnElement($contents);
    }

    public function getColumnHtmlObjectEditMode()
    {
        $contents = $this->getContents(true);

        return $this->getColumnElement($contents);
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
