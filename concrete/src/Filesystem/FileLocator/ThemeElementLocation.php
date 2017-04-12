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
                . DIRECTORY_SEPARATOR
                . $this->pkgHandle
                . DIRECTORY_SEPARATOR
                . DIRNAME_THEMES
                . DIRECTORY_SEPARATOR
                . $this->themeHandle
                . DIRECTORY_SEPARATOR
                . DIRNAME_ELEMENTS
                . DIRECTORY_SEPARATOR
                . 'concrete';
        } else {
            return DIR_APPLICATION
                . DIRECTORY_SEPARATOR
                . DIRNAME_THEMES
                . DIRECTORY_SEPARATOR
                . $this->themeHandle
                . DIRECTORY_SEPARATOR
                . DIRNAME_ELEMENTS
                . DIRECTORY_SEPARATOR
                . 'concrete';
        }
    }

    public function contains($file)
    {
        // Since we are testing this in a special way, we strip DIRNAME_ELEMENTS off the front.
        $length = strlen(DIRNAME_ELEMENTS . DIRECTORY_SEPARATOR);
        $file = substr($file, $length);
        return parent::contains($file);
    }

    public function getURL()
    {
        if ($this->pkgHandle) {
            return DIR_REL
            . DIRECTORY_SEPARATOR
            . $this->pkgHandle
            . DIRECTORY_SEPARATOR
            . DIRNAME_THEMES
            . DIRECTORY_SEPARATOR
            . $this->themeHandle
            . DIRECTORY_SEPARATOR
            . DIRNAME_ELEMENTS
            . DIRECTORY_SEPARATOR
            . 'concrete';
        } else {
            return DIR_REL
            . DIRECTORY_SEPARATOR
            . DIRNAME_THEMES
            . DIRECTORY_SEPARATOR
            . $this->themeHandle
            . DIRECTORY_SEPARATOR
            . DIRNAME_ELEMENTS
            . DIRECTORY_SEPARATOR
            . 'concrete';
        }
    }
}
