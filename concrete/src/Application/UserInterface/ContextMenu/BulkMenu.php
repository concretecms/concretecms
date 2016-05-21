<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu;

class BulkMenu implements BulkMenuInterface
{

    protected $propertyName;
    protected $propertyValue;
    protected $menu;

    /**
     * @return mixed
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param mixed $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    public function getPropertyName()
    {
        return $this->propertyName;
    }

    public function getPropertyValue()
    {
        return $this->propertyValue;
    }

    public function setPropertyName($propertyName)
    {
        return $this->propertyName = $propertyName;
    }

    public function setPropertyValue($propertyValue)
    {
        return $this->propertyValue = $propertyValue;
    }

    public function jsonSerialize()
    {
        $data = array();
        $data['propertyName'] = $this->getPropertyName();
        $data['propertyValue'] = $this->getPropertyValue();
        $data['menu'] = $this->getMenu();
        return $data;
    }



}