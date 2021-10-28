<?php

namespace Concrete\StartingPointPackage\ElementalBlank;

use Concrete\Core\Package\StartingPointPackage;

class controller extends StartingPointPackage
{
    protected $pkgHandle = 'elemental_blank';

    public function getPackageName()
    {
        return t('Empty Site (Elemental)');
    }

    public function getPackageDescription()
    {
        return t('Creates an empty site using the unstyled Elemental theme.');
    }
}
