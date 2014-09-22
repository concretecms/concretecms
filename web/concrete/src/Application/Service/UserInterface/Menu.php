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
        $menuItemControllerHandle,
        $pkgHandle = false,
        $options = array()
    ) {

        $defaults = array(
            'icon' => 'share',
            'label' => false,
            'position' => 'right',
            'href' => false,
            'linkAttributes' => array()
        );

        $options = array_merge($defaults, $options);

        $obj = new Item($menuItemControllerHandle, $pkgHandle);
        $obj->setLabel($options['label']);
        $obj->setPosition($options['position']);
        $obj->setIcon($options['icon']);
        if ($options['href']) {
            $obj->setLink($options['href']);
        }
        $obj->setLinkAttributes($options['linkAttributes']);
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

