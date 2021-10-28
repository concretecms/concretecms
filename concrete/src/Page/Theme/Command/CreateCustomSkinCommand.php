<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;

class CreateCustomSkinCommand extends Command
{

    /**
     * @var string
     */
    protected $skinName;

    /**
     * @var int
     */
    protected $themeID;

    /**
     * @var string
     */
    protected $presetStartingPoint;

    /**
     * @var string
     */
    protected $customCss;

    /**
     * @var int
     */
    protected $authorID;

    /**
     * The styles data as posted by the customizer
     *
     * @var array
     */
    protected $styles;

    /**
     * @return string
     */
    public function getSkinName(): string
    {
        return $this->skinName;
    }

    /**
     * @param string $skinName
     */
    public function setSkinName(string $skinName): void
    {
        $this->skinName = $skinName;
    }

    /**
     * @return string
     */
    public function getCustomCss(): string
    {
        return $this->customCss;
    }

    /**
     * @param string $customCss
     */
    public function setCustomCss(string $customCss): void
    {
        $this->customCss = $customCss;
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

    /**
     * @return int
     */
    public function getAuthorID(): int
    {
        return $this->authorID;
    }

    /**
     * @param int $authorID
     */
    public function setAuthorID(int $authorID): void
    {
        $this->authorID = $authorID;
    }

    /**
     * @return string
     */
    public function getPresetStartingPoint(): string
    {
        return $this->presetStartingPoint;
    }

    /**
     * @param string $presetStartingPoint
     */
    public function setPresetStartingPoint(string $presetStartingPoint): void
    {
        $this->presetStartingPoint = $presetStartingPoint;
    }

    /**
     * @return array
     */
    public function getStyles(): array
    {
        return $this->styles;
    }

    /**
     * @param array $styles
     */
    public function setStyles(array $styles): void
    {
        $this->styles = $styles;
    }



}
