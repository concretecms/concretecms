<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item;

use Concrete\Core\Application\UserInterface\Welcome\ContentItem\AbstractStandardItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action\VideoAction;

class PowerMovesItem extends AbstractStandardItem
{

    public function getTitle(): string
    {
        return t('Time-Saving Power Moves');
    }

    public function getDescription(): string
    {
        return t('Tips and tricks to make your site easier to manage.');
    }

    public function getActions(): array
    {
        return [
            new VideoAction('https://www.youtube.com/watch?v=SRVOcZyvFvE')
        ];
    }

}
