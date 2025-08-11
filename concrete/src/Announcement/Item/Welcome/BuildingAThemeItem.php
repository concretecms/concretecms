<?php

namespace Concrete\Core\Announcement\Item\Welcome;

use Concrete\Core\Announcement\Action\LearnMoreAction;
use Concrete\Core\Announcement\Item\AbstractStandardItem;

class BuildingAThemeItem extends AbstractStandardItem
{

    public function getTitle(): string
    {
        return t('Building a Theme');
    }

    public function getDescription(): string
    {
        return t('Learn how page types, templates and themes work so you can make your own.');
    }

    public function getActions(): array
    {
        return [
            new LearnMoreAction('https://documentation.concretecms.org/building-website-concretecms'),
        ];
    }
}
