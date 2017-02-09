<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Filesystem\TemplateLocator;

class BasicFormViewContext extends ViewContext
{

    public function setLocation(TemplateLocator $locator)
    {
        $locator->setTemplate('bootstrap3');
        return $locator;
    }


}
