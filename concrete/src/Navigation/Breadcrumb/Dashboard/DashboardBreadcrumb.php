<?php

namespace Concrete\Core\Navigation\Breadcrumb\Dashboard;

use Concrete\Core\Navigation\Breadcrumb\BreadcrumbInterface;
use Concrete\Core\Navigation\Item\ItemInterface;
use Concrete\Core\Navigation\NavigationInterface;

class DashboardBreadcrumb implements BreadcrumbInterface, \Iterator
{
    /**
     * @var \Concrete\Core\Navigation\Item\ItemInterface[]
     */
    protected $items = [];

    /**
     * @var bool
     */
    protected $sanitizeName = true;

    /**
     * @return bool
     */
    public function sanitizeName(): bool
    {
        return $this->sanitizeName;
    }

    /**
     * @param bool $sanitizeName
     */
    public function setSanitizeName(bool $sanitizeName): void
    {
        $this->sanitizeName = $sanitizeName;
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
     */
    public function setItems(array $items): NavigationInterface
    {
        $this->items = [];
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
    }

    public function rewind()
    {
        reset($this->items);
    }

    public function current()
    {
        return current($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        next($this->items);
    }

    public function valid()
    {
        return $this->current() !== false;
    }
}
