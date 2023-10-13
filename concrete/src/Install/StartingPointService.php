<?php

namespace Concrete\Core\Install;

use Concrete\Core\Application\Application;
use Concrete\Core\File\Service\File;
use Concrete\Core\Install\StartingPoint\StartingPointInterface;

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

    /**
     * @var StartingPointFactory
     */
    protected $startingPointFactory;

    /**
     * @var StartingPointInterface[]
     */
    protected $startingPoints;

    public function __construct(StartingPointFactory $startingPointFactory, Application $app, File $fileService)
    {
        $this->startingPointFactory = $startingPointFactory;
        $this->fileService = $fileService;
        $this->app = $app;
        $this->loadStartingPoints();
    }

    /**
     * @return StartingPointInterface[]
     */
    public function getStartingPoints(): array
    {
        return $this->startingPoints;
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
                $startingPoint = $this->startingPointFactory->createFromClass($directory, $pkg);
                if ($startingPoint) {
                    $startingPoints[] = $startingPoint;
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
                            $startingPoint = $this->startingPointFactory->createFromThemeClass($directory, $theme, $themeHandle);
                            if ($startingPoint) {
                                $startingPoints[] = $startingPoint;
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
            $startingPoint = $this->startingPointFactory->createFromClass($directory, $pkg);
            if ($startingPoint) {
                $startingPoints[] = $startingPoint;
            }
        }

        $this->startingPoints = $startingPoints;
    }

    public function getByHandle(string $handle): ?StartingPointInterface
    {
        foreach ($this->startingPoints as $startingPoint) {
            if ($startingPoint->getHandle() === $handle) {
                return $startingPoint;
            }
        }
        return null;
    }


}
