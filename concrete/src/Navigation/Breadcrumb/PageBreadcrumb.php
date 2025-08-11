<?php

namespace Concrete\Core\Navigation\Breadcrumb;

use Concrete\Core\Navigation\Item\ItemInterface;
use Concrete\Core\Navigation\NavigationInterface;

class PageBreadcrumb implements BreadcrumbInterface, \Iterator
{
    /**
     * @var \Concrete\Core\Navigation\Item\ItemInterface[]
     */
    protected $items = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Navigation\NavigationInterface::add()
     * @return self
     */
    public function add(ItemInterface $item): NavigationInterface
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Navigation\NavigationInterface::getItems()
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Navigation\NavigationInterface::setItems()
     * @return self
     */
    public function setItems(array $items): NavigationInterface
    {
        $this->items = [];
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::rewind()
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->items);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::current()
     *
     * @return \Concrete\Core\Navigation\Item\ItemInterface|false
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->items);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::key()
     *
     * @return int|string
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->items);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::next()
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        next($this->items);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::valid()
     */
    public function valid(): bool
    {
        return $this->current() !== false;
    }
}
