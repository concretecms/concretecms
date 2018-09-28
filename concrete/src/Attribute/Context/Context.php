<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Filesystem\TemplateLocator;

abstract class Context implements ContextInterface
{

    protected $actions = [];
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
     */
    public function getControlTemplates()
    {
        return $this->controlTemplates;
    }

    public function setLocation(TemplateLocator $locator)
    {
        return $locator;
    }

    public function render(Key $key, $value = null)
    {
        return;
    }


}
