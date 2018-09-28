<?php
namespace Concrete\Core\Form\Context;

use Concrete\Core\Filesystem\TemplateLocator;

interface ContextInterface
{

    /**
     * @param TemplateLocator $locator
     * @return TemplateLocator
     */
    function setLocation(TemplateLocator $locator);


}
