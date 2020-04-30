<?php
namespace Concrete\Core\Navigation\Breadcrumb\Dashboard;

use Concrete\Core\Navigation\Breadcrumb\BreadcrumbInterface;
use Concrete\Core\Navigation\Item\ItemInterface;

class DashboardBreadcrumb implements BreadcrumbInterface
{

    /**
     * @var ItemInterface[]
     */
    protected $items = [];

    public function add(ItemInterface $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return ItemInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

}
