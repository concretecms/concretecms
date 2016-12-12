<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

interface ControllerInterface
{

    /**
     * Determine whether item should be displayed
     *
     * @return bool
     */
    public function displayItem();

    /**
     * @return void
     */
    public function registerViewAssets();

    /**
     * @return \HtmlObject\Traits\Tag
     */
    public function getMenuItemLinkElement();

    /**
     * @param ItemInterface $item
     * @return void
     */
    public function setMenuItem(ItemInterface $item);

    /**
     * @return ItemInterface
     */
    public function getMenuItem();

}
