<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Foundation\Command\Command;

/**
 * This command is used by the legacy non-skin-based customizer.
 * @deprecated – use the skin based customizer instead.
 *
 * Class ApplyCustomizationsToSiteCommand
 * @package Concrete\Core\Page\Theme\Command
 */
class ApplyCustomizationsToSiteCommand extends Command
{

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
     * The styles data as posted by the customizer
     *
     * @var array
     */
    protected $styles;

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
