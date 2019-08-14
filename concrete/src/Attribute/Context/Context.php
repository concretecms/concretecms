<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Filesystem\TemplateLocator;

/**
 * @since 8.0.0
 */
abstract class Context implements ContextInterface
{

    protected $actions = [];
    /**
     * @since 8.2.0
     */
    protected $controlTemplates = [];

    public function preferTemplateIfAvailable($template, $pkgHandle = null)
    {
        array_unshift($this->controlTemplates, [$template, $pkgHandle]);
    }

    public function preferActionIfAvailable($action)
    {
        array_unshift($this->actions, $action);
    }

    public function includeTemplateIfAvailable($template, $pkgHandle = null)
    {
        $this->controlTemplates[] = [$template, $pkgHandle];
    }

    public function runActionIfAvailable($action)
    {
        $this->actions[] = $action;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @return array
     * @since 8.2.0
     */
    public function getControlTemplates()
    {
        return $this->controlTemplates;
    }

    /**
     * @since 8.2.0
     */
    public function setLocation(TemplateLocator $locator)
    {
        return $locator;
    }

    /**
     * @since 8.2.0
     */
    public function render(Key $key, $value = null)
    {
        return;
    }


}
