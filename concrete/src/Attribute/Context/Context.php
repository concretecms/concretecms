<?php
namespace Concrete\Core\Attribute\Context;

class Context implements ContextInterface
{

    protected $actions = [];

    protected $templates = [];

    public function preferTemplateIfAvailable($template, $pkgHandle = null)
    {
        array_unshift($this->templates, [$template, $pkgHandle]);
    }

    public function preferActionIfAvailable($action)
    {
        array_unshift($this->actions, $action);
    }

    public function includeTemplateIfAvailable($template, $pkgHandle = null)
    {
        $this->templates[] = [$template, $pkgHandle];
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
    public function getTemplates()
    {
        return $this->templates;
    }

}
