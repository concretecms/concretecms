<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

use HtmlObject\Element;

/**
 * @since 8.0.0
 */
interface MenuInterface extends \JsonSerializable
{

    /**
     * @return Element
     */
    function getMenuElement();

}