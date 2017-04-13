<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

use Concrete\Core\Package\Package;

class Item implements ItemInterface
{
    protected $controller = null;

    protected $linkAttributes = [];

    protected $handle = null;

    protected $label = null;

    protected $position = null;

    protected $href = null;

    protected $icon = null;

    protected $pkgHandle = null;

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
