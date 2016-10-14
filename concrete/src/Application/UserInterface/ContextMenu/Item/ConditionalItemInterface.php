<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu\Item;

interface ConditionalItemInterface extends ItemInterface
{

    /**
     * @return bool
     */
    function displayItem();


}