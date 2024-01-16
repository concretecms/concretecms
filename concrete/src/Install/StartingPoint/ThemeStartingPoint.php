<?php

namespace Concrete\Core\Install\StartingPoint;

use Concrete\Core\Install\StartingPoint\Installer\Installer;
use Concrete\Core\Install\StartingPoint\Installer\InstallerInterface;
use Concrete\Core\Install\StartingPoint\Installer\PackagedThemeInstaller;
use Concrete\Core\Package\Package;
use Concrete\Core\Page\Theme\Theme;

class ThemeStartingPoint extends AbstractStartingPoint
{

    /**
     * @var Theme
     */
    protected $theme;

    /**
     * @var string
     */
    protected $themeHandle;

    /**
     * @var Package
     */
    protected $pkg;

    /**
     * StartingPoint constructor.
     */
    public function __construct(string $directory, Package $packageController, Theme $theme, string $themeHandle)
    {
        $this->directory = $directory;
        $this->theme = $theme;
        $this->pkg = $packageController;
        $this->themeHandle = $themeHandle;
    }

    public function getHandle(): string
    {
        return $this->themeHandle;
    }

    /**
     * @return Package
     */
    public function getPackage(): Package
    {
        return $this->pkg;
    }

    public function getName(): string
    {
        return $this->theme->getThemeName();
    }

    public function getThumbnail(): ?string
    {
        return null;
    }

    public function providesThumbnails(): bool
    {
        return false;
    }

    public function getDescription()
    {
        return $this->theme->getThemeDescription();
    }

    public function getInstaller(): InstallerInterface
    {
        return new Installer();
    }

}
