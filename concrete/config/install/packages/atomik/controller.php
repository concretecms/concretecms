<?php

namespace Concrete\StartingPointPackage\Atomik;

use Concrete\Core\Install\StartingPoint\Controller\AbstractController;

class Controller extends AbstractController
{

    public function getHandle(): string
    {
        return 'atomik';
    }

    public function getName(): string
    {
        return t('Atomik');
    }

    public function getThumbnail(): ?string
    {
        return ASSETS_URL . '/' . DIRNAME_THEMES . '/atomik/thumbnail.png';
    }

    public function getDescription()
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
