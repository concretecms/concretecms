<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

use HtmlObject\Element;

/**
 * @since 8.0.0
 */
interface BulkMenuInterface extends \JsonSerializable
{

    function getPropertyName();
    function getPropertyValue();

    /**
     * @return MenuInterface
     */
    function getMenu();


}