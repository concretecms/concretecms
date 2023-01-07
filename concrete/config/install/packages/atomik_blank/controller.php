<?php

namespace Concrete\StartingPointPackage\AtomikBlank;

use Concrete\Core\Package\StartingPointPackage;

class Controller extends StartingPointPackage
{
    protected $pkgHandle = 'atomik_blank';

    public function getPackageName()
    {
        return t('Empty Site');
    }

    public function getPackageDescription()
    {
        return t('Creates an empty site using the Atomik theme.');
    }
}
