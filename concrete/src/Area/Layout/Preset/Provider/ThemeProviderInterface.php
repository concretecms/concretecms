<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

/**
 * @since 5.7.5
 */
interface ThemeProviderInterface
{
    public function getThemeAreaLayoutPresets();
    public function getThemeName();
    public function getThemeHandle();
}
