<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Modal;

use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\View\View;

class IntroductionModal extends AbstractBasicModal
{

    public function getTitle(): string
    {
        return t('Take a few minutes to learn the basics');
    }

    public function getBody(): string
    {
        return $this->app->make(ElementManager::class)
            ->get('help/introduction')
            ->getContents();
    }
}
