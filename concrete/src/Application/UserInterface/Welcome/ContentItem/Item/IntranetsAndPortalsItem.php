<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item;

use Concrete\Core\Application\UserInterface\Welcome\ContentItem\AbstractStandardItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action\LearnMoreAction;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action\VideoAction;

class IntranetsAndPortalsItem extends AbstractStandardItem
{

    public function getTitle(): string
    {
        return t('Intranets & Portals');
    }

    public function getDescription(): string
    {
        return t('Get your team on the same page.');
    }

    public function getActions(): array
    {
        return [
            new VideoAction('https://www.youtube.com/watch?v=W2vJjyE5RGg'),
            new LearnMoreAction('https://www.concretecms.com/intranets'),
        ];
    }

}
