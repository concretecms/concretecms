<?php

namespace Concrete\Core\Announcement\Item\Welcome;

use Concrete\Core\Announcement\Action\VideoAction;
use Concrete\Core\Announcement\Item\AbstractStandardItem;

class FindingYourLookItem extends AbstractStandardItem
{

    public function getTitle(): string
    {
        return t('Finding Your Look');
    }

    public function getDescription(): string
    {
        return t('You can customize this theme, or browse our marketplace.');
    }

    public function getActions(): array
    {
        return [
            new VideoAction('https://www.youtube.com/watch?v=wElKyPNmV-k')
        ];
    }

}
