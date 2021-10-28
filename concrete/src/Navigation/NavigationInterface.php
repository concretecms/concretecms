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

    /**
     * Replace all the existing items with new ones.
     *
     * @param \Concrete\Core\Navigation\Item\ItemInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items): self;
}
