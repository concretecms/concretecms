<?php
namespace Concrete\Core\Navigation\Modifier;

use Concrete\Core\Navigation\NavigationInterface;

class TopLevelOnlyModifier implements ModifierInterface
{

    /**
     * Returns just the top level navigation of the dashboard. Used in the dashboard panel.
     */
    public function modify(NavigationInterface $navigation)
    {
        foreach($navigation->getItems() as $topLevelItem) {
            $topLevelItem->setChildren([]);
        }
    }

}
