<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

/**
 * @since 5.7.2.1
 */
interface ItemInterface
{
    /**
     * @param ControllerInterface $controller
     */
    public function setController(ControllerInterface $controller);

    /**
     * @return ControllerInterface
     */
    public function getController();

    /**
     * @return bool
     */
    public function getPosition();
}
