<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\ItemGroupInterface;
use Concrete\Core\Application\UserInterface\ContextMenu\Modifier\ModifierInterface;

abstract class AbstractManager implements ManagerInterface
{

    /**
     * @var ModifierInterface[]
     */
    protected $modifiers = array();

    public function addMenuModifier(ModifierInterface $modifier)
    {
        $this->modifiers[] = $modifier;
    }

    /**
     * @param MenuInterface $menu
     * @return \HtmlObject\Element
     */
    final public function deliverMenu(ModifiableMenuInterface $menu)
    {
        foreach($this->modifiers as $modifier) {
            $modifier->modifyMenu($menu);
        }
        return $menu->getMenuElement();
    }


}