<?php

namespace Concrete\Core\Install;

use Concrete\Core\Install\StartingPoint\Controller\ControllerInterface;
use Concrete\Core\Install\StartingPoint\LegacyStartingPoint;
use Concrete\Core\Install\StartingPoint\StartingPoint;
use Concrete\Core\Install\StartingPoint\StartingPointInterface;
use Concrete\Core\Install\StartingPoint\ThemeStartingPoint;
use Concrete\Core\Package\StartingPointPackage;

class StartingPointFactory
{

    public function createFromThemeClass(string $directory, $class, $themeHandle): ?StartingPointInterface
    {
        return new ThemeStartingPoint($directory, $class, $themeHandle);
    }

    public function createFromClass(string $directory, $class): ?StartingPointInterface
    {

        $startingPoint = null;
        if ($class instanceof ControllerInterface) {
            $startingPoint = new StartingPoint($directory, $class);
        } else if ($class instanceof StartingPointPackage) {
            // Legacy support
            $startingPoint = new LegacyStartingPoint($directory, $class);
        }

        return $startingPoint;
    }

}
