<?php

namespace Concrete\Core\Announcement\Item\Welcome;

use Concrete\Core\Announcement\Action\VideoAction;
use Concrete\Core\Announcement\Item\AbstractStandardItem;

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
