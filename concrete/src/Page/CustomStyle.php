<?php
namespace Concrete\Core\Page;

use Concrete\Core\StyleCustomizer\Style\ValueList;
use Concrete\Core\StyleCustomizer\CustomCssRecord;
use Concrete\Core\Page\Theme\Theme;

class CustomStyle
{
    protected $pThemeID;
    protected $valueListID;
    protected $presetHandle;
    protected $sccRecordID;

    public function setThemeID($pThemeID)
    {
        $this->pThemeID = $pThemeID;
    }

    public function setValueListID($valueListID)
    {
        $this->valueListID = $valueListID;
    }

    public function setPresetHandle($presetHandle)
    {
        $this->presetHandle = $presetHandle;
    }

    public function setCustomCssRecordID($sccRecordID)
    {
        $this->sccRecordID = $sccRecordID;
    }

    public function getValueList()
    {
        return ValueList::getByID($this->valueListID);
    }

    public function getPresetHandle()
    {
        return $this->presetHandle;
    }

    public function getTheme()
    {
        if ($this->pThemeID > 0) {
            $theme = Theme::getByID($this->pThemeID);

            return $theme;
        }
    }
    public function getCustomCssRecord()
    {
        if ($this->sccRecordID > 0) {
            $css = CustomCssRecord::getByID($this->sccRecordID);

            return $css;
        }
    }
}
