<?php

namespace Concrete\Core\Navigation\Modifier;

use Concrete\Core\Navigation\NavigationInterface;

class FlatChildrenModifier implements ModifierInterface
{

    private function flatChildren($childPages)
    {
        $flattedChildPages = [];

        foreach ($childPages as $childPage) {
            $flattedChildPages[] = $childPage;

            if (count($childPage->getChildren()) > 0) {
                $flattedChildPages = array_merge($flattedChildPages, $this->flatChildren($childPage->getChildren()));
            }
        }

        return $flattedChildPages;
    }

    public function modify(NavigationInterface $navigation)
    {
        foreach ($navigation->getItems() as $item) {
            $item->setChildren($this->flatChildren($item->getChildren()));
        }
    }

}
