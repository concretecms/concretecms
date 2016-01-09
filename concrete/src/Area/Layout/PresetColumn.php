<?php
namespace Concrete\Core\Area\Layout;

use HtmlObject\Element;
use Loader;

class PresetColumn extends Column
{
    /**
     * @param int $arLayoutColumnID
     * @return static
     */
    public static function getByID($arLayoutColumnID)
    {
        $al = new static();
        $al->loadBasicInformation($arLayoutColumnID);
        return $al;
    }

    /**
     * @param \SimpleXMLElement $node
     */
    public function exportDetails($node)
    {
    }

    public function getAreaLayoutColumnClass()
    {
        return '';
    }

    protected function getPresetObject()
    {
        if (!isset($this->preset)) {
            $this->preset = $this->getAreaLayoutObject()->getPresetObject();
        }
        return $this->preset;
    }
    protected function getPresetColumnObject()
    {
        $preset = $this->getPresetObject();
        if (is_object($preset)) {
            $index = $this->getAreaLayoutColumnIndex();
            $columns = $preset->getColumns();
            if (isset($columns[$index])) {
                return $columns[$index];
            }
        }
        return new Element('div');
    }

    public function getColumnHtmlObject()
    {
        $column = $this->getPresetColumnObject();
        if ($column) {
            $inner = $column->getColumnHtmlObject();
            $inner->setValue($this->getContents());
            return $inner;
        }
    }

    public function getColumnHtmlObjectEditMode()
    {
        $column = $this->getPresetColumnObject();
        if ($column) {
            $html = $column->getColumnHtmlObject();
            $inner = new Element('div');
            $inner->addClass('ccm-layout-column-inner ccm-layout-column-highlight');
            $inner->setValue($this->getContents(true));
            $html->appendChild($inner);
            return $html;
        }
    }

}