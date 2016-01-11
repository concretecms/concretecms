<?php
namespace Concrete\Core\Area\Layout;

use Loader;
use Page;

class ThemeGridLayout extends Layout
{
    /**
     * @var string
     */
    protected $arLayoutType = 'theme_grid';

    /**
     * @var int
     */
    protected $arLayoutMaxColumns;

    /**
     * @var int
     */
    protected $arLayoutSpacing;

    /**
     * @var bool
     */
    protected $arLayoutIsCustom;

    /**
     * @var \Concrete\Core\Page\Theme\GridFramework\GridFramework
     */
    protected $gf;

    protected function loadDetails()
    {
        $db = Loader::db();
        $row = $db->GetRow('select arLayoutMaxColumns from AreaLayouts where arLayoutID = ?', array($this->arLayoutID));
        $this->setPropertiesFromArray($row);

        $c = Page::getCurrentPage();
        if (is_object($c)) {
            $pt = $c->getCollectionThemeObject();
            if (is_object($pt) && $pt->supportsGridFramework()) {
                $gf = $pt->getThemeGridFrameworkObject();
                $this->setThemeGridFrameworkObject($gf);
            }
        }
    }

    /**
     * @param \SimpleXMLElement $node
     */
    public function exportDetails($node)
    {
        $node->addAttribute('columns', $this->arLayoutMaxColumns);
    }

    /**
     * @param \Concrete\Core\Page\Theme\GridFramework\GridFramework $gf
     */
    public function setThemeGridFrameworkObject($gf)
    {
        $this->gf = $gf;
    }

    /**
     * @return \Concrete\Core\Page\Theme\GridFramework\GridFramework
     */
    public function getThemeGridFrameworkObject()
    {
        return $this->gf;
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
     * @return bool
     */
    public function isAreaLayoutUsingThemeGridFramework()
    {
        return $this->arLayoutUsesThemeGridFramework;
    }

    /**
     * @return CustomLayout|ThemeGridLayout|null
     */
    public function duplicate()
    {
        $db = Loader::db();
        $v = array($this->arLayoutMaxColumns, 1);
        $db->Execute('insert into AreaLayouts (arLayoutMaxColumns, arLayoutUsesThemeGridFramework) values (?, ?)', $v);
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
     * @param int $max
     */
    public function setAreaLayoutMaxColumns($max)
    {
        if (!$max) {
            $max = 0;
        }
        $db = Loader::db();
        $db->Execute('update AreaLayouts set arLayoutMaxColumns = ? where arLayoutID = ?', array($max, $this->arLayoutID));
        $this->arLayoutMaxColumns = $max;
    }

    /**
     * @return int
     */
    public function getAreaLayoutMaxColumns()
    {
        return $this->arLayoutMaxColumns;
    }

    /**
     * @return static
     */
    public function addLayoutColumn()
    {
        $columnID = parent::addLayoutColumn();
        $db = Loader::db();
        $db->Execute('insert into AreaLayoutThemeGridColumns (arLayoutColumnID) values (?)', array($columnID));

        return ThemeGridColumn::getByID($columnID);
    }

    /**
     * @return CustomLayout|ThemeGridLayout|null
     */
    public static function add()
    {
        $db = Loader::db();
        $db->Execute('insert into AreaLayouts (arLayoutSpacing, arLayoutIsCustom, arLayoutUsesThemeGridFramework) values (?, ?, ?)', array(0, 0, 1));
        $arLayoutID = $db->Insert_ID();
        if ($arLayoutID) {
            $ar = static::getByID($arLayoutID);

            return $ar;
        }
    }
}
