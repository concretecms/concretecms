<?php
namespace Concrete\Core\Area\Layout;

use Concrete\Core\Html\Object\Collection;
use HtmlObject\Element;
use Loader;

class ThemeGridColumn extends Column
{
    /**
     * @var int
     */
    public $arLayoutColumnSpan;

    /**
     * @var int
     */
    public $arLayoutColumnOffset;

    /**
     * @param int $arLayoutColumnID
     *
     * @return static
     */
    public static function getByID($arLayoutColumnID)
    {
        $db = Loader::db();
        $row = $db->GetRow('select * from AreaLayoutThemeGridColumns where arLayoutColumnID = ?', array($arLayoutColumnID));
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
     * @return ThemeGridColumn
     */
    public function duplicate($newAreaLayout)
    {
        $areaLayoutColumnID = parent::duplicate($newAreaLayout);
        $db = Loader::db();
        $v = array($areaLayoutColumnID, $this->arLayoutColumnSpan, $this->arLayoutColumnOffset);
        $db->Execute('insert into AreaLayoutThemeGridColumns (arLayoutColumnID, arLayoutColumnSpan, arLayoutColumnOffset) values (?, ?, ?)', $v);
        $newAreaLayoutColumn = self::getByID($areaLayoutColumnID);

        return $newAreaLayoutColumn;
    }

    protected function getSubAreaMaximumColumns()
    {
        $framework = $this->getAreaLayoutObject()->getThemeGridFrameworkObject();
        if (is_object($framework) && $framework->supportsNesting()) {
            return $framework->getPageThemeGridFrameworkNumColumns();
        }
    }

    /**
     * @param \SimpleXMLElement $node
     */
    public function exportDetails($node)
    {
        $node->addAttribute('span', $this->arLayoutColumnSpan);
        $node->addAttribute('offset', $this->arLayoutColumnOffset);
    }

    /**
     * @return int
     */
    public function getAreaLayoutColumnSpan()
    {
        return $this->arLayoutColumnSpan;
    }

    /**
     * @return int
     */
    public function getAreaLayoutColumnOffset()
    {
        return $this->arLayoutColumnOffset;
    }

    /**
     * @return string
     */
    public function getAreaLayoutColumnClass()
    {
        /*
         * @var ThemeGridLayout $this->arLayout
         */
        $gf = $this->arLayout->getThemeGridFrameworkObject();
        if (is_object($gf)) {
            $class = $gf->getPageThemeGridFrameworkColumnAdditionalClasses();
            if ($class) {
                $class .= ' ';
            }

            // the width parameter of the column becomes the span
            $class .= $gf->getPageThemeGridFrameworkColumnClassForSpan($this->arLayoutColumnSpan);

            return $class;
        }
    }

    /**
     * this returns offsets in the form of spans.
     *
     * @return string
     */
    public function getAreaLayoutColumnOffsetEditClass()
    {
        /*
         * @var ThemeGridLayout $this->arLayout
         */
        $gf = $this->arLayout->getThemeGridFrameworkObject();
        if (is_object($gf)) {
            $class = $gf->getPageThemeGridFrameworkColumnAdditionalClasses();
            if ($class) {
                $class .= ' ';
            }

            $class .= $gf->getPageThemeGridFrameworkColumnClassForSpan($this->arLayoutColumnOffset);

            return $class;
        }
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

    protected function getColumnElement($contents)
    {
        $element = new Element('div');
        $element->addClass($this->getAreaLayoutColumnClass());
        $gf = $this->arLayout->getThemeGridFrameworkObject();
        if (is_object($gf) && $gf->hasPageThemeGridFrameworkOffsetClasses() && $this->getAreaLayoutColumnOffset()) {
            $element->addClass($this->getAreaLayoutColumnOffsetClass());
        }
        $element->setValue($contents);
        if (is_object($gf) && $this->getAreaLayoutColumnOffset() > 0 && (!$gf->hasPageThemeGridFrameworkOffsetClasses())) {
            $collection = new Collection();
            $offset = new Element('div');
            $offset->addClass($this->getAreaLayoutColumnOffsetClass())
                ->addClass('ccm-theme-grid-offset-column');
            $collection[0] = $offset;
            $collection[1] = $element;

            return $collection;
        } else {
            return $element;
        }
    }

    /**
     * @return string
     */
    public function getAreaLayoutColumnOffsetClass()
    {
        $gf = $this->arLayout->getThemeGridFrameworkObject();
        if (is_object($gf)) {
            // the width parameter of the column becomes the span
            $class = $gf->getPageThemeGridFrameworkColumnOffsetAdditionalClasses();
            if ($class) {
                $class .= ' ';
            }
            if ($gf->hasPageThemeGridFrameworkOffsetClasses()) {
                $class .= $gf->getPageThemeGridFrameworkColumnClassForOffset($this->arLayoutColumnOffset);
            } else {
                $class .= $gf->getPageThemeGridFrameworkColumnClassForSpan($this->arLayoutColumnOffset);
            }

            return $class;
        }
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute("delete from AreaLayoutThemeGridColumns where arLayoutColumnID = ?", array($this->arLayoutColumnID));
        parent::delete();
    }

    /**
     * @param int $span
     */
    public function setAreaLayoutColumnSpan($span)
    {
        if (!$span) {
            $span = 0;
        }
        $db = Loader::db();
        $db->Execute('update AreaLayoutThemeGridColumns set arLayoutColumnSpan = ? where arLayoutColumnID = ?', array($span, $this->arLayoutColumnID));
        $this->arLayoutColumnSpan = $span;
    }

    /**
     * @param int $offset
     */
    public function setAreaLayoutColumnOffset($offset)
    {
        if (!$offset) {
            $offset = 0;
        }
        $db = Loader::db();
        $db->Execute(
            'update AreaLayoutThemeGridColumns set arLayoutColumnOffset = ? where arLayoutColumnID = ?',
            array($offset, $this->arLayoutColumnID)
        );
        $this->arLayoutColumnOffset = $offset;
    }
}
