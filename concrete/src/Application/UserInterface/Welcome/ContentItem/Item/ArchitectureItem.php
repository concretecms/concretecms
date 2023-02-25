<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item;

use Concrete\Core\Application\UserInterface\Welcome\ContentItem\AbstractStandardItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action\LearnMoreAction;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action\VideoAction;

class ArchitectureItem extends AbstractStandardItem
{

    public function getTitle(): string
    {
        return t('Architecture of Concrete CMS');
    }

    public function getDescription(): string
    {
        return t("Learn the basics of the Concrete's technical architecture.");
    }

    public function getActions(): array
    {
        return [
            new VideoAction('https://www.youtube.com/watch?v=vcIIM5ZgzP8'),
            new LearnMoreAction('https://documentation.concretecms.org/building-website-concretecms'),
        ];
    }
}
