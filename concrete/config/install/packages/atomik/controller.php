<?php

namespace Concrete\StartingPointPackage\Atomik;

use Concrete\Core\Install\StartingPoint\Controller\AbstractController;

class Controller extends AbstractController
{

    public function getStartingPointHandle(): string
    {
        return 'atomik';
    }

    public function getStartingPointName(): string
    {
        return t('Atomik');
    }

    public function getStartingPointThumbnail(): ?string
    {
        return ASSETS_URL . '/' . DIRNAME_THEMES . '/atomik/thumbnail.png';
    }

    public function getStartingPointDescription()
    {
        return [
            t('Creative Services'),
            t('Company Websites'),
            t('Marketing & Products'),
            t('Corporate Blogs'),
            t('General purpose websites'),
        ];
    }

}
