<?php

namespace Concrete\Core\Navigation;

use Concrete\Core\Navigation\Modifier\ModifierInterface;

/**
 * Class NavigationModifier
 *
 * Responsible for modifying a navigation tree, and creating a cloned copy of it.
 *
 */
class NavigationModifier
{

    protected $modifiers = [];

    public function addModifier(ModifierInterface $modifier)
    {
        $this->modifiers[] = $modifier;
    }

    public function process(NavigationInterface $navigation): NavigationInterface
    {
        $navigation = clone $navigation;
        foreach ($this->modifiers as $modifier) {
            $modifier->modify($navigation);
        }
        return $navigation;
    }


}
