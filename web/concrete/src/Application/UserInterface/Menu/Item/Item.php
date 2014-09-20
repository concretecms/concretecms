<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

class Item
{

    public function __construct($handle, $name, $position, $linkAttributes, $pkgHandle = false)
    {
        $this->handle = $handle;
        $this->name = $name;
        $this->position = $position;
        $this->linkAttributes = $linkAttributes;
        $this->pkgHandle = $pkgHandle;
    }

    protected $controller;

    public function getHandle()
    {
        return $this->handle;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getLinkAttributes()
    {
        return $this->linkAttributes;
    }

    public function getPackageObject()
    {
        return $this->pkgHandle;
    }

    public function getController()
    {
        if (isset($this->controller)) {
            return $this->controller;
        } else {
            $class = overrideable_core_class(
                'MenuItem\\' . camelcase($this->handle) . '\\Controller',
                DIRNAME_MENU_ITEMS . '/' . $this->handle . '/' . FILENAME_CONTROLLER,
                $this->pkgHandle
            );
            $this->controller = \Core::make($class, array($this));
            $this->controller->setMenuItem($this);
            return $this->controller;
        }
    }

}
