<?php
namespace Concrete\Core\Form\Control;

use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Form\Context\ContextInterface;

interface ViewInterface
{

    /**
     * @return RendererInterface
     */
    function getControlRenderer();

    /**
     * @return TemplateLocator
     */
    function createTemplateLocator();

    /**
     * @return ContextInterface
     */
    function getContext();

    function getScopeItems();

}
