<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu\Item;

use HtmlObject\Element;

/**
 * @since 8.0.0
 */
class DividerItem extends AbstractItem
{

    public function getItemElement()
    {
        return (new Element('li'))->addClass('divider');
    }

}