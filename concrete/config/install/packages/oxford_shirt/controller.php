<?php

namespace Concrete\StartingPointPackage\OxfordShirt;

use Concrete\Core\Install\StartingPoint\Controller\AbstractController;

class Controller extends AbstractController
{
    public function getStartingPointName(): string
    {
        return t('Oxford Shirt');
    }

    public function getStartingPointHandle(): string
    {
        return 'oxford_shirt';
    }

    public function getStartingPointThumbnail(): ?string
    {
        return ASSETS_URL . '/' . DIRNAME_THEMES . '/oxford_shirt/thumbnail.png';
    }

    public function getStartingPointDescription(): array
    {
        return [
            t('Intranets'),
            t('Portals'),
            t('Communication Hubs'),
            t('Corporate Blogs'),
            t('General purpose websites'),
        ];
    }


}
