<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item;

use Concrete\Core\Application\UserInterface\Welcome\ContentItem\AbstractStandardItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action\GuideAction;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action\LearnMoreAction;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action\VideoAction;

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
