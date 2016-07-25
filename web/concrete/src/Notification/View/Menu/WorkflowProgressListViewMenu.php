<?php
namespace Concrete\Core\Notification\View\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\ItemInterface;
use Concrete\Core\Application\UserInterface\ContextMenu\Menu;
use HtmlObject\Element;

class WorkflowProgressListViewMenu extends Menu
{

    public function getMenuElement()
    {
        $list = (new Element('ul'))->addClass('dropdown-menu');

        /**
         * @var $item ItemInterface
         */
        foreach($this->items as $item) {
            $list->appendChild($item->getItemElement());
        }

        return $list;
    }


}