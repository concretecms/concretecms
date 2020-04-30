<?php
namespace Concrete\Core\Navigation;

use Concrete\Core\Navigation\Item\ItemInterface;

interface NavigationInterface
{

    /**
     * Adds an item to the navigation.
     *
     * @param ItemInterface $item
     * @return mixed
     */
    public function add(ItemInterface $item);

    /**
     * Returns all the items in the navigation
     *
     * @return ItemInterface[]
     */
    public function getItems(): array;

}
