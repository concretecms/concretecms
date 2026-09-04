<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

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
     * @return string|null
     */
    public function getPosition();
}
