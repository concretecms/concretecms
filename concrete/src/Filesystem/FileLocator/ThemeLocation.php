<?php
namespace Concrete\Core\Filesystem\FileLocator;

use Concrete\Core\Package\PackageList;
use Concrete\Core\Page\Theme\Theme;
use Illuminate\Filesystem\Filesystem;

class ThemeLocation extends AbstractLocation
{

    protected $filesystem;
    protected $pkgHandle;
    protected $themeHandle;

    public function getCacheKey()
    {
        return array('theme', $this->themeHandle);
    }

    public function __construct(Theme $theme)
    {
        $this->themeHandle = $theme->getThemeHandle();
        $this->pkgHandle = $theme->getPackageHandle();
    }

    /**
     * @return mixed
     */
    public function getThemeHandle()
    {
        return $this->themeHandle;
    }

    public function getPath()
    {
        if ($this->pkgHandle) {
            return DIR_PACKAGES
                . DIRECTORY_SEPARATOR
                . $this->pkgHandle
                . DIRECTORY_SEPARATOR
                . DIRNAME_THEMES
                . DIRECTORY_SEPARATOR
                . $this->themeHandle;
        } else {
            return DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_THEMES . DIRECTORY_SEPARATOR . $this->themeHandle;
        }
    }

    public function getURL()
    {
        if ($this->pkgHandle) {
            return DIR_REL
            . DIRECTORY_SEPARATOR
            . $this->pkgHandle
            . DIRECTORY_SEPARATOR
            . DIRNAME_THEMES
            . $this->themeHandle;
        } else {
            return DIR_REL . DIRECTORY_SEPARATOR . DIRNAME_THEMES . DIRECTORY_SEPARATOR . $this->themeHandle;
        }
    }
}
