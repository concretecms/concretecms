<?php
namespace Concrete\Core\Navigation;

use Concrete\Core\Navigation\Item\ItemInterface;

class Navigation implements  NavigationInterface
{

    /**
     * @var ItemInterface[]
     */
    protected $items = [];

    /**
     * Adds an item to the navigation.
     *
     * @param ItemInterface $item
     * @return mixed
     */
    public function add(ItemInterface $item)
    {
        $this->items[] = $item;
    }

    /**
     * Returns all the items in the navigation
     *
     * @return ItemInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

}
