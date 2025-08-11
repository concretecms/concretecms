<?php

namespace Concrete\Core\Announcement\Item\Welcome;

use Concrete\Core\Announcement\Action\LearnMoreAction;
use Concrete\Core\Announcement\Action\VideoAction;
use Concrete\Core\Announcement\Item\AbstractStandardItem;

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
