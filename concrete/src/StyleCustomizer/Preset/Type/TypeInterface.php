<?php

namespace Concrete\Core\StyleCustomizer\Preset\Type;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Preset\PresetInterface;

interface TypeInterface
{

    /**
     * @param string $path
     * @param Theme $theme
     * @return PresetInterface
     */
    public function createPresetFromPath(string $path, Theme $theme): PresetInterface;

    /**
     * @param PresetInterface $preset
     * @return string
     */
    public function getVariablesFile(PresetInterface $preset): string;

    /**
     * @param PresetInterface $preset
     * @return string
     */
    public function getEntryPointFile(PresetInterface $preset): string;

}
