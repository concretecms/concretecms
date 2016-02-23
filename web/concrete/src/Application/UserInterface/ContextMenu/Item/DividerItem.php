<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu\Item;

use HtmlObject\Element;

class DividerItem extends AbstractItem
{

    public function getItemElement()
    {
        return (new Element('li'))->addClass('divider');
    }

}