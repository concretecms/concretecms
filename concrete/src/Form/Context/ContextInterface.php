<?php
namespace Concrete\Core\Form\Context;

use Concrete\Core\Filesystem\TemplateLocator;

/**
 * @since 8.2.0
 */
interface ContextInterface
{

    /**
     * @param TemplateLocator $locator
     * @return TemplateLocator
     */
    function setLocation(TemplateLocator $locator);


}
