<?php
namespace Concrete\Core\Navigation;

use Concrete\Core\Navigation\Item\ItemInterface;

class Navigation implements  NavigationInterface, \JsonSerializable
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

    /**
     * @param ItemInterface[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function jsonSerialize()
    {
        $data = [];
        foreach($this->getItems() as $item) {
            $data[] = $item->jsonSerialize();
        }
        return $data;
    }

    public function __clone()
    {
        $items = $this->getItems();
        $this->setItems([]);
        foreach($items as $item) {
            $this->add(clone $item);
        }
    }



}
