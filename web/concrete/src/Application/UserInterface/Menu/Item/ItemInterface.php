<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

interface ItemInterface
{

    /**
     * @param ControllerInterface $controller
     * @return void
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
