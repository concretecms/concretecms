<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

use HtmlObject\Element;

interface BulkMenuInterface extends \JsonSerializable
{

    function getPropertyName();
    function getPropertyValue();

    /**
     * @return MenuInterface
     */
    function getMenu();


}