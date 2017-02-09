<?php
namespace Concrete\Core\Form\Context;

use Concrete\Core\Attribute\Form\Control\View;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Form\Group\ViewInterface;

interface ContextInterface
{

    /**
     * @param TemplateLocator $locator
     * @return TemplateLocator
     */
    function setLocation(TemplateLocator $locator);


}
