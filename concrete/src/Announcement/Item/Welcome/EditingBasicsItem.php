<?php

namespace Concrete\Core\Announcement\Item\Welcome;

use Concrete\Core\Announcement\Action\GuideAction;
use Concrete\Core\Announcement\Action\VideoAction;
use Concrete\Core\Announcement\Item\AbstractStandardItem;

class EditingBasicsItem extends AbstractStandardItem
{

    public function getTitle(): string
    {
        return t('The Basics of Editing');
    }

    public function getDescription(): string
    {
        return t('Change content, build new pages.');
    }

    public function getActions(): array
    {
        return [
            new VideoAction('https://www.youtube.com/watch?v=IYW8DufIpTU'),
            new GuideAction('toolbar'),
        ];
    }
}
