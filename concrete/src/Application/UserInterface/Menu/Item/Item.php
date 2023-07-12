<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

use Concrete\Core\Package\Package;

class Item implements ItemInterface
{
    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var string|null
     */
    public $handle;

    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var string|false|null
     */
    public $pkgHandle;

    /**
     * @var \Concrete\Core\Application\UserInterface\Menu\Item\ControllerInterface|null
     */
    protected $controller;

    protected $linkAttributes = [];

    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var string|null
     */
    public $position;

    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var string|null
     */
    public $href;

    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var string|null
     */
    public $label;

    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var string|null
     */
    public $icon;

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
        if ($this->controller === null) {
            $handle = $this->getHandle();
            $class = overrideable_core_class(
                'MenuItem\\' . camelcase($handle) . '\\Controller',
                DIRNAME_MENU_ITEMS . '/' . $handle . '/' . FILENAME_CONTROLLER,
                $this->pkgHandle
            );
            $this->setController(\Core::make($class, ['item' => $this]));
        }

        return $this->controller;
    }

    public function setController(ControllerInterface $controller)
    {
        $this->controller = $controller;
        $this->controller->setMenuItem($this);
    }
}
