<?php
namespace Concrete\Core\Notification\View\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\ItemInterface;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use HtmlObject\Element;

class WorkflowProgressListViewMenu extends DropdownMenu
{
    public function getMenuElement()
    {
        $menu = parent::getMenuElement();
        $menu->appendChild((new DividerItem())->getItemElement());

        $item = new LinkItem(
            '#',
            tc('Verb', 'Archive'),
            ['data-notification-action' => 'archive']
        );
        $menu->appendChild($item->getItemElement());

        return $menu;
    }
}
