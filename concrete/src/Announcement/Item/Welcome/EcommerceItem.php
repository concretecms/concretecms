<?php

namespace Concrete\Core\Announcement\Item\Welcome;

use Concrete\Core\Announcement\Action\LearnMoreAction;
use Concrete\Core\Announcement\Item\AbstractStandardItem;

class EcommerceItem extends AbstractStandardItem
{

    public function getTitle(): string
    {
        return t('Start Building a Custom Store');
    }

    public function getDescription(): string
    {
        return t('Concrete lets a developer build powerful ecommerce sites.');
    }

    public function getActions(): array
    {
        return [
            new LearnMoreAction('https://www.concretecms.com/applications/ecommerce'),
        ];
    }

}
