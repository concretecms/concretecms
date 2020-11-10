<?php

namespace Concrete\Core\Navigation;

use Concrete\Core\Navigation\Item\Item;
use League\Fractal\TransformerAbstract;

class NavigationItemTransformer extends TransformerAbstract
{
    /**
     * Basic transforming of a Navigation item into an array.
     *
     * @param Item $item
     *
     * @return array
     */
    public function transform(Item $item)
    {
        return $item->jsonSerialize();
    }
}
