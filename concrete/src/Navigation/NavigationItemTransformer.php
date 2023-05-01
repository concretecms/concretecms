<?php

namespace Concrete\Core\Navigation;

use Concrete\Core\Navigation\Item\SerializableItemInterface;
use League\Fractal\TransformerAbstract;

class NavigationItemTransformer extends TransformerAbstract
{
    /**
     * Basic transforming of a Navigation item into an array.
     *
     * @param SerializableItemInterface $item
     *
     * @return array
     */
    public function transform(SerializableItemInterface $item)
    {
        return $item->jsonSerialize();
    }
}
