<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item;

use Concrete\Core\Application\UserInterface\Welcome\ContentItem\AbstractStandardItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action\GuideAction;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action\VideoAction;

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
