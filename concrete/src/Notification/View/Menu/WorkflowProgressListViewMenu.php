<?php
namespace Concrete\Core\Notification\View\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\ItemInterface;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Menu;
use HtmlObject\Element;

class WorkflowProgressListViewMenu extends Menu
{
    public function getMenuElement()
    {
        $list = (new Element('ul'))->addClass('dropdown-menu');

        foreach ($this->items as $item) {
            /* @var ItemInterface $item */
            $list->appendChild($item->getItemElement());
        }

        $list->appendChild((new DividerItem())->getItemElement());

        $item = new LinkItem(
            '#',
            tc('Verb', 'Archive'),
            ['data-notification-action' => 'archive']
        );
        $list->appendChild($item->getItemElement());

        return $list;
    }
}
