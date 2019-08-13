<?php
namespace Concrete\Core\Form\Control;

use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Form\Context\ContextInterface;

/**
 * @since 8.2.0
 */
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
