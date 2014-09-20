<?php

namespace Concrete\Core\Application\Service\UserInterface;

use Concrete\Core\Application\UserInterface\Menu\Item\Item;

class Menu
{

    protected $pageHeaderMenuItems = array();

    /**
     * Adds a menu item to the header menu area
     * <code>
     *    $bh->addMenuItem($menuItemID, $menuItemName, $positionInMenu, $linkAttributes, $pkgHandle = false);
     * </code>
     */
    public function addPageHeaderMenuItem(
        $menuItemID,
        $menuItemName,
        $positionInMenu,
        $linkAttributes,
        $pkgHandle = false
    ) {
        $obj = new Item($menuItemID, $menuItemName, $positionInMenu, $linkAttributes, $pkgHandle);
        $this->pageHeaderMenuItems[] = $obj;
    }

    /**
     * Returns current menu items
     */
    public function getPageHeaderMenuItems($position = false)
    {
        if ($position) {
            $tmpItems = array();
            foreach ($this->pageHeaderMenuItems as $mi) {
                if ($mi->getPosition() == $position) {
                    $tmpItems[] = $mi;
                }
            }
            return $tmpItems;
        } else {
            return $this->pageHeaderMenuItems;
        }
    }

}

