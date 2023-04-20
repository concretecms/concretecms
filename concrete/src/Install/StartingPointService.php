<?php

namespace Concrete\Core\Install;

use Concrete\Core\Application\Application;
use Concrete\Core\File\Service\File;
use Concrete\Core\Install\StartingPoint\Controller\ControllerInterface;
use Concrete\Core\Install\StartingPoint\StartingPoint;
use Concrete\Core\Package\StartingPointPackage;

class StartingPointService
{

    /**
     * @var File
     */
    protected $fileService;

    /**
     * @var Application
     */
    protected $app;

    protected $featuredStartingPoints = [];

    protected $otherStartingPoints = [];

    public function __construct(Application $app, File $fileService)
    {
        $this->fileService = $fileService;
        $this->app = $app;
        $this->loadStartingPoints();
    }

    public function getFeaturedStartingPoints(): array
    {
        return $this->featuredStartingPoints;
    }

    public function getOtherStartingPoints(): array
    {
        return $this->otherStartingPoints;
    }

    protected function createStartingPoint(string $directory, string $handle, string $name, $description, $thumbnail = null)
    {
        return new StartingPoint($directory, $handle, $name, $description, $thumbnail);
    }

    public function getStartingPointFromHandle(string $handle)
    {
        foreach ($this->featuredStartingPoints as $startingPoint) {
            if ($startingPoint->getHandle() == $handle) {
                return $startingPoint;
            }
        }
        foreach ($this->otherStartingPoints as $startingPoint) {
            if ($startingPoint->getHandle() == $handle) {
                return $startingPoint;
            }
        }
    }

    protected function loadStartingPoints()
    {
        $startingPoints = [];
        // First spot: check application/config/install for any custom starting points.
        if (is_dir(DIR_STARTING_POINT_PACKAGES)) {
            foreach ($this->fileService->getDirectoryContents(DIR_STARTING_POINT_PACKAGES) as $pkgHandle) {
                $class = '\\Application\\StartingPointPackage\\' . camelcase($pkgHandle) . '\\Controller';
                $pkg = $this->app->make($class);
                $directory = DIR_STARTING_POINT_PACKAGES . DIRECTORY_SEPARATOR . $pkgHandle;
                if ($pkg instanceof ControllerInterface) {
                    $startingPoints[] = $this->createStartingPoint($directory, $pkg->getStartingPointHandle(), $pkg->getStartingPointName(), $pkg->getStartingPointDescription(), $pkg->getStartingPointThumbnail());
                } else {
                    /**
                     * @var $pkg StartingPointPackage
                     */
                    $startingPoints[] = $this->createStartingPoint($directory, $pkg->getPackageHandle(), $pkg->getPackageName(), $pkg->getPackageDescription());
                }
            }
        }

        // Next, check the themes directory for any themes that have the ability to reset content. They can then be
        // installed and activated.
        foreach ($this->fileService->getDirectoryContents(DIR_PACKAGES) as $pkgHandle) {
            $themeDirectory = DIR_PACKAGES . DIRECTORY_SEPARATOR . $pkgHandle . DIRECTORY_SEPARATOR . DIRNAME_THEMES;
            if (is_dir($themeDirectory)) {
                foreach ($this->fileService->getDirectoryContents($themeDirectory) as $themeHandle) {
                    $themeClass = $themeDirectory . DIRECTORY_SEPARATOR . $themeHandle . DIRECTORY_SEPARATOR . FILENAME_THEMES_CLASS;
                    if (is_file($themeClass)) {
                        $pkgClass = DIR_PACKAGES . DIRECTORY_SEPARATOR . $pkgHandle . DIRECTORY_SEPARATOR . FILENAME_CONTROLLER;
                        require_once($pkgClass); // not ideal, but autoloading doesn't work because the package isn't installed.
                        require_once($themeClass); // see above
                        $pkgClass = '\\Concrete\\Package\\' . camelcase($pkgHandle) . '\\Controller';
                        $themeClass = '\\Concrete\\Package\\' . camelcase($pkgHandle) . '\\Theme\\' . camelcase($themeHandle) . '\\PageTheme';
                        $pkg = $this->app->make($pkgClass);
                        $theme = $this->app->make($themeClass);
                        if ($pkg->allowsFullContentSwap()) {
                            $directory = DIR_PACKAGES . DIRECTORY_SEPARATOR . $pkgHandle;
                            if ($pkg instanceof ControllerInterface) {
                                $startingPoints[] = $this->createStartingPoint($directory, $pkg->getStartingPointThumbnail(), $pkg->getStartingPointName(), $pkg->getStartingPointDescription(), $pkg->getStartingPointThumbnail());
                            } else {
                                $startingPoints[] = $this->createStartingPoint($directory, $themeHandle, $theme->getThemeName(), $theme->getThemeDescription());
                            }
                        }
                    }
                }
            }
        }

        // Finally, check for starting points found at concrete/config/install/
        foreach ($this->fileService->getDirectoryContents(DIR_STARTING_POINT_PACKAGES_CORE) as $pkgHandle) {
            $class = '\\Concrete\\StartingPointPackage\\' . camelcase($pkgHandle) . '\\Controller';
            $pkg = $this->app->make($class);
            $directory = DIR_STARTING_POINT_PACKAGES_CORE . DIRECTORY_SEPARATOR . $pkgHandle;
            if ($pkg instanceof ControllerInterface) {
                $startingPoints[] = $this->createStartingPoint($directory, $pkg->getStartingPointHandle(), $pkg->getStartingPointName(), $pkg->getStartingPointDescription(), $pkg->getStartingPointThumbnail());
            } else {
                $startingPoints[] = $this->createStartingPoint($directory, $pkg->getPackageHandle(), $pkg->getPackageName(), $pkg->getPackageDescription());
            }
        }

        foreach ($startingPoints as $startingPoint) {
            if ($startingPoint->getThumbnail()) {
                $this->featuredStartingPoints[] = $startingPoint;
            } else {
                $this->otherStartingPoints[] = $startingPoint;
            }
        }
    }

    /*
     * @TODO - replace this with a better method that can work in all the contexts.
     */
    public function getController(string $identifier)
    {
        if (is_dir(DIR_STARTING_POINT_PACKAGES . '/' . $identifier)) {
            $class = '\\Application\\StartingPointPackage\\' . camelcase($identifier) . '\\Controller';
        } else {
            $class = '\\Concrete\\StartingPointPackage\\' . camelcase($identifier) . '\\Controller';
        }
        if (class_exists($class, true)) {
            $cl = $this->app->build($class);
        } else {
            $cl = null;
        }
        return $cl;
    }


}
