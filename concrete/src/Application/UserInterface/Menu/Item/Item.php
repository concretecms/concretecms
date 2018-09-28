<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

use Concrete\Core\Package\Package;

class Item implements ItemInterface
{
    protected $controller;

    protected $linkAttributes = [];

    public function __construct($handle, $pkgHandle = false)
    {
        $this->handle = $handle;
        $this->pkgHandle = $pkgHandle;
    }

    public function getHandle()
    {
        return isset($this->handle) ? $this->handle : null;
    }

    public function getLabel()
    {
        return isset($this->label) ? $this->label : null;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getPosition()
    {
        return isset($this->position) ? $this->position : null;
    }

    public function getLinkAttributes()
    {
        return isset($this->linkAttributes) ? $this->linkAttributes : null;
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
        return isset($this->href) ? $this->href : null;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function getIcon()
    {
        return isset($this->icon) ? $this->icon : null;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPackageHandle()
    {
        return isset($this->pkgHandle) ? $this->pkgHandle : null;
    }

    public function getPackageObject()
    {
        return Package::getByHandle($this->getPackageHandle());
    }

    public function getController()
    {
        if (!isset($this->controller)) {
            $handle = $this->getHandle();
            $class = overrideable_core_class(
                'MenuItem\\' . camelcase($handle) . '\\Controller',
                DIRNAME_MENU_ITEMS . '/' . $handle . '/' . FILENAME_CONTROLLER,
                $this->pkgHandle
            );
            $this->setController(\Core::make($class, [$this]));
        }

        return $this->controller;
    }

    public function setController(ControllerInterface $controller)
    {
        $this->controller = $controller;
        $this->controller->setMenuItem($this);
    }
}
