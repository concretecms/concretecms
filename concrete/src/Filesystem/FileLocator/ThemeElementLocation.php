<?php
namespace Concrete\Core\Filesystem\FileLocator;

use Concrete\Core\Package\PackageList;
use Concrete\Core\Page\Theme\Theme;
use Illuminate\Filesystem\Filesystem;

class ThemeElementLocation extends ThemeLocation
{

    public function getPath()
    {
        if ($this->pkgHandle) {
            return DIR_PACKAGES
                . '/'
                . $this->pkgHandle
                . '/'
                . DIRNAME_THEMES
                . '/'
                . $this->themeHandle;

        } else {
            return DIR_APPLICATION
                . '/'
                . DIRNAME_THEMES
                . '/'
                . $this->themeHandle;
        }
    }

    public function getURL()
    {
        if ($this->pkgHandle) {
            return DIR_REL
            . '/'
            . $this->pkgHandle
            . '/'
            . DIRNAME_THEMES
            . '/'
            . $this->themeHandle;
        } else {
            return DIR_REL
            . '/'
            . DIRNAME_THEMES
            . '/'
            . $this->themeHandle;
        }
    }
}
