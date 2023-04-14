<?php

namespace Concrete\Core\Install;

use Concrete\Core\Package\StartingPointPackage;

class StartingPointService
{

    /**
     * @return self[]
     */
    public function getStartingPoints(): array
    {
        $packages = StartingPointPackage::getAvailableList();
        $startingPoints = [];
        foreach ($packages as $pkg) {
            $startingPoints[] = new StartingPoint($pkg->getPackageName(), $pkg->getPackageHandle(), $pkg->getPackageDescription());
        }
        return $startingPoints;
    }


}
