<?php

namespace Concrete\Core\Navigation;

use Concrete\Core\Navigation\Item\ItemInterface;

interface NavigationInterface
{
    /**
     * Adds an item to the navigation.
     *
     * @param \Concrete\Core\Navigation\Item\ItemInterface $item
     *
     * @return $this
     */
    public function add(ItemInterface $item): self;

    /**
     * Returns all the items in the navigation.
     *
     * @return \Concrete\Core\Navigation\Item\ItemInterface[]
     */
    public function getItems(): array;
}
