<?php

namespace Concrete\Core\Announcement\Item\Welcome;

use Concrete\Core\Announcement\Action\LearnMoreAction;
use Concrete\Core\Announcement\Action\VideoAction;
use Concrete\Core\Announcement\Item\AbstractStandardItem;

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
