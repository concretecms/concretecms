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
                . '/'
                . $this->pkgHandle
                . '/'
                . DIRNAME_THEMES
                . '/'
                . $this->themeHandle;
        } else {
            return DIR_APPLICATION . '/' . DIRNAME_THEMES . '/' . $this->themeHandle;
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
            . $this->themeHandle;
        } else {
            return DIR_REL . '/' . DIRNAME_THEMES . '/' . $this->themeHandle;
        }
    }
}
