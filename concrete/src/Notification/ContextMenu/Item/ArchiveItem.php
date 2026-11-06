<?php

namespace Concrete\Core\Notification\ContextMenu\Item;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\AbstractItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use HtmlObject\Link;

class ArchiveItem extends AbstractItem
{

    public function getItemElement()
    {
        $item = new LinkItem(
            '#',
            t('Delete from list'),
            ['data-notification-action' => 'archive']
        );
        return $item->getItemElement();
    }

}
