<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item;

use Concrete\Core\Application\UserInterface\Welcome\ContentItem\AbstractStandardItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Action\LearnMoreAction;

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
