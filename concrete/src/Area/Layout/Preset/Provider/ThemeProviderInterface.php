<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

interface ThemeProviderInterface
{

    public function getThemeAreaLayoutPresets();
    public function getThemeName();
    public function getThemeHandle();

}