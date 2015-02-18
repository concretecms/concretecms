<?php

namespace Concrete\StartingPointPackage\ElementalFull;

use \Concrete\Core\Package\StartingPointPackage;

class Controller extends StartingPointPackage
{

    protected $pkgHandle = 'elemental_full';
    protected $pkgContentProvidesFileThumbnails = true;

    public function getPackageName()
    {
        return t('Full Site');
    }

    public function getPackageDescription()
    {
        return t('Creates a full website with the Elemental theme, including a home page, multiple page types, portfolio, contact forms, blogs and more..');
    }

}