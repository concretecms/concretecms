<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\Foundation\Command\Command;

class GenerateCustomSkinStylesheetCommand extends Command
{

    /**
     * @var CustomSkin
     */
    protected $customSkin;

    /**
     * @var int
     */
    protected $themeID;

    /**
     * @return CustomSkin
     */
    public function getCustomSkin(): CustomSkin
    {
        return $this->customSkin;
    }

    /**
     * @param CustomSkin $customSkin
     */
    public function setCustomSkin(CustomSkin $customSkin): void
    {
        $this->customSkin = $customSkin;
    }

    /**
     * @return int
     */
    public function getThemeID(): int
    {
        return $this->themeID;
    }

    /**
     * @param int $themeID
     */
    public function setThemeID(int $themeID): void
    {
        $this->themeID = $themeID;
    }







}
