<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Filesystem\TemplateLocator;

/**
 * @since 8.2.0
 */
class BasicFormViewContext extends ViewContext
{

    public function setLocation(TemplateLocator $locator)
    {
        $locator->setTemplate('bootstrap3');
        return $locator;
    }


}
