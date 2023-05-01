<?php

namespace Concrete\Core\Navigation\Modifier;

use ClassesWithParents\E;
use Concrete\Core\Navigation\Item\SupportsChildrenItemInterface;
use Concrete\Core\Navigation\NavigationInterface;

class FlatChildrenModifier implements ModifierInterface
{

    private function flatChildren($childPages)
    {
        $flattedChildPages = [];

        foreach ($childPages as $childPage) {
            $flattedChildPages[] = $childPage;
            if ($childPage instanceof SupportsChildrenItemInterface) {
                if (count($childPage->getChildren()) > 0) {
                    $flattedChildPages = array_merge(
                        $flattedChildPages,
                        $this->flatChildren($childPage->getChildren())
                    );
                }
            }
        }

        return $flattedChildPages;
    }

    public function modify(NavigationInterface $navigation)
    {
        foreach ($navigation->getItems() as $item) {
            if ($item instanceof SupportsChildrenItemInterface) {
                $item->setChildren($this->flatChildren($item->getChildren()));
            }
        }
    }

}
