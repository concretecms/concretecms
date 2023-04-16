<?php

namespace Concrete\Core\Install;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\File\Service\File;
use Concrete\Core\Install\StartingPoint\FeaturedStartingPoint;
use Concrete\Core\Install\StartingPoint\StartingPoint;
use Concrete\Core\Package\FeaturedStartingPointPackageInterface;
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

    protected function createFeaturedStartingPoint(string $thumbnail, string $identifier, string $name, array $descriptionLines)
    {
        return new FeaturedStartingPoint($thumbnail, $identifier, $name, $descriptionLines);
    }

    public function createStartingPoint(string $identifier, string $name, string $description)
    {
        return new StartingPoint($identifier, $name, $description);
    }

    protected function loadStartingPoints()
    {
        $startingPoints = [];
        // First spot: check application/config/install for any custom starting points.
        if (is_dir(DIR_STARTING_POINT_PACKAGES)) {
            foreach ($this->fileService->getDirectoryContents(DIR_STARTING_POINT_PACKAGES) as $pkgHandle) {
                $class = '\\Application\\StartingPointPackage\\' . camelcase($pkgHandle) . '\\Controller';
                $pkg = $this->app->make($class);
                if ($pkg instanceof FeaturedStartingPointPackageInterface) {
                    $startingPoints[] = $this->createFeaturedStartingPoint($pkg->getStartingPointThumbnail(), $pkg->getPackageHandle(), $pkg->getPackageName(), $pkg->getStartingPointDescriptionLines());
                } else {
                    $startingPoints[] = $this->createStartingPoint($pkg->getPackageHandle(), $pkg->getPackageName(), $pkg->getPackageDescription());
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
                            if ($pkg instanceof FeaturedStartingPointPackageInterface) {
                                $startingPoints[] = $this->createFeaturedStartingPoint($pkg->getStartingPointThumbnail(), $themeHandle, $theme->getThemeName(), $pkg->getStartingPointDescriptionLines());
                            } else {
                                $startingPoints[] = $this->createStartingPoint($themeHandle, $theme->getThemeName(), $theme->getThemeDescription());
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
            if ($pkg instanceof FeaturedStartingPointPackageInterface) {
                $startingPoints[] = $this->createFeaturedStartingPoint($pkg->getStartingPointThumbnail(), $pkg->getPackageHandle(), $pkg->getPackageName(), $pkg->getStartingPointDescriptionLines());
            } else {
                $startingPoints[] = $this->createStartingPoint($pkg->getPackageHandle(), $pkg->getPackageName(), $pkg->getPackageDescription());
            }
        }

        foreach ($startingPoints as $startingPoint) {
            if ($startingPoint instanceof FeaturedStartingPoint) {
                $this->featuredStartingPoints[] = $startingPoint;
            } else {
                $this->otherStartingPoints[] = $startingPoint;
            }
        }
    }

    /*
     * @TODO - replace this with a better method that can work in all the contexts.
     */
    public function getController(string $identifier): StartingPointPackage
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
