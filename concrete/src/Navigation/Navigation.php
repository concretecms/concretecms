<?php

namespace Concrete\Core\Navigation;

use Concrete\Core\Navigation\Item\ItemInterface;
use JsonSerializable;

class Navigation implements NavigationInterface, JsonSerializable
{
    /**
     * @var \Concrete\Core\Navigation\Item\ItemInterface[]
     */
    protected $items = [];

    public function __clone()
    {
        $items = $this->getItems();
        $this->setItems([]);
        foreach ($items as $item) {
            $this->add(clone $item);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Navigation\NavigationInterface::add()
     */
    public function add(ItemInterface $item): NavigationInterface
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Returns all the items in the navigation.
     *
     * @return \Concrete\Core\Navigation\Item\ItemInterface[]
     */
    public function getItems(): array
    {
        return (array) $this->items;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Navigation\NavigationInterface::setItems()
     */
    public function setItems(array $items): NavigationInterface
    {
        $this->items = $items;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [];
        foreach ($this->getItems() as $item) {
            $data[] = $item->jsonSerialize();
        }

        return $data;
    }
}
