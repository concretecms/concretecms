<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;
use Concrete\Core\Package\Package;
use Core;

class Item implements ItemInterface
{

    protected $controller;

    public function __construct($handle, $pkgHandle = false)
    {
        $this->handle = $handle;
        $this->pkgHandle = $pkgHandle;
    }

    public function getHandle()
    {
        return $this->handle;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getLinkAttributes()
    {
        return $this->linkAttributes;
    }

    public function setLinkAttributes($linkAttributes)
    {
        $this->linkAttributes = $linkAttributes;
    }

    public function setLink($href)
    {
        $this->href = $href;
    }

    public function getLink()
    {
        return $this->href;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    public function getPackageObject()
    {
        return Package::getByHandle($this->pkgHandle);
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
            $this->setController(\Core::make($class, array($this)));
            return $this->controller;
        }
    }

    public function setController(ControllerInterface $controller)
    {
        $this->controller = $controller;
        $this->controller->setMenuItem($this);
    }

}
