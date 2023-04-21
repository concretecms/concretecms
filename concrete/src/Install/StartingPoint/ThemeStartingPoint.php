<?php

namespace Concrete\Core\Install\StartingPoint;

use Concrete\Core\Install\StartingPoint\Installer\InstallerInterface;
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
     * StartingPoint constructor.
     */
    public function __construct(string $directory, Theme $theme, string $themeHandle)
    {
        $this->directory = $directory;
        $this->theme = $theme;
        $this->themeHandle = $themeHandle;
    }

    public function getHandle(): string
    {
        return $this->themeHandle;
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

    }

}
