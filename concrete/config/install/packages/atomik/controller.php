<?php

namespace Concrete\StartingPointPackage\Atomik;

use Concrete\Core\Package\FeaturedStartingPointPackageInterface;
use Concrete\Core\Package\StartingPointPackage;

class Controller extends StartingPointPackage implements FeaturedStartingPointPackageInterface
{
    protected $pkgHandle = 'atomik';

    public function getStartingPointThumbnail(): string
    {
        return ASSETS_URL . '/' . DIRNAME_THEMES . '/atomik/thumbnail.png';
    }

    public function getStartingPointDescriptionLines(): array
    {
        return [
            t('Creative Services'),
            t('Company Websites'),
            t('Marketing & Products'),
            t('Corporate Blogs'),
            t('General purpose websites'),
        ];
    }

    public function getPackageName()
    {
        return t('Atomik');
    }

}
