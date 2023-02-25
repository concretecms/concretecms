<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item;

use Concrete\Core\Application\UserInterface\Welcome\ContentItem\AbstractStandardItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action\VideoAction;

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
