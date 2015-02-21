<?php
namespace Concrete\Core\Application\Service\UserInterface;

use Concrete\Core\Application\UserInterface\Menu\Item\Item;
use Concrete\Core\Application\UserInterface\Menu\Item\ItemInterface;

class Menu
{

    /**
     * @var ItemInterface[]
     */
    protected $pageHeaderMenuItems = array();

    /**
     * @param string $menuItemControllerHandle
     * @param bool   $pkgHandle
     * @param array  $options
     * @return Item
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
        $this->addMenuItem($obj);

        return $obj;
    }

    /**
     * @param ItemInterface $item
     */
    public function addMenuItem(ItemInterface $item)
    {
        $this->pageHeaderMenuItems[] = $item;
    }


    /**
     * Returns current menu items
     *
     * @param bool $position
     * @return ItemInterface[]
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

