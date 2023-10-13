<?php

namespace Concrete\StartingPointPackage\Elemental;

use Concrete\Core\Package\StartingPointPackage;

class Controller extends StartingPointPackage
{
    protected $pkgHandle = 'elemental';
    protected $pkgContentProvidesFileThumbnails = true;

    public function getPackageName()
    {
        return t('Elemental');
    }

    public function getPackageDescription()
    {
        return t('Installs Concrete using the Elemental theme. Elemental was the default Concrete CMS theme from 2014 to 2020 and is suitable for general purpose websites.');
    }
}
