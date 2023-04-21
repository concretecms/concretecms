<?php

namespace Concrete\StartingPointPackage\OxfordShirt;

use Concrete\Core\Install\StartingPoint\Controller\AbstractController;

class Controller extends AbstractController
{

    public function providesThumbnails(): bool
    {
        return true;
    }

    public function getName(): string
    {
        return t('Oxford Shirt');
    }

    public function getHandle(): string
    {
        return 'oxford_shirt';
    }

    public function getThumbnail(): ?string
    {
        return ASSETS_URL . '/' . DIRNAME_THEMES . '/oxford_shirt/thumbnail.png';
    }

    public function getDescription(): array
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
