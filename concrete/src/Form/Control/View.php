<?php
namespace Concrete\Core\Form\Control;

use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Form\Context\ContextInterface;

abstract class View implements ViewInterface
{

    protected $context;
    protected $scopeItems = array();

    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
    }

    public function addScopeItem($key, $value)
    {
        $this->scopeItems[$key] = $value;
    }

    public function getScopeItems()
    {
        return $this->scopeItems;
    }

    public function getView()
    {
        return $this;
    }

    /**
     * @return ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    public function getControlRenderer()
    {
        $renderer = new Renderer($this->getView(), $this->context);
        return $renderer;
    }


}
