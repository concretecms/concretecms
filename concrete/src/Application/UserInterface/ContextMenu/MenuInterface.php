<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

use HtmlObject\Element;

interface MenuInterface extends \JsonSerializable
{

    /**
     * @return Element
     */
    function getMenuElement();

}