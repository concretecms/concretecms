<?php

namespace Concrete\StartingPointPackage\AtomikFull;

use Concrete\Core\Package\StartingPointPackage;

class controller extends StartingPointPackage
{
    protected $pkgHandle = 'atomik_full';

    public function getPackageName()
    {
        return t('Atomik Theme');
    }

    public function getPackageDescription()
    {
        return t('Creates a full services agency site using the new Atomik theme.');
    }
}
