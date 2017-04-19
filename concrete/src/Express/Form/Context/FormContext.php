<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Attribute\Context\BasicFormContext;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\Form\Group\FormView;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Form\Control\ControlInterface;

class FormContext extends ViewContext
{

    public function getAttributeContext()
    {
        return new BasicFormContext();
    }

    public function setLocation(TemplateLocator $locator)
    {
        $locator = parent::setLocation($locator);
        $locator->prependLocation(DIRNAME_ELEMENTS .
            DIRECTORY_SEPARATOR .
            DIRNAME_EXPRESS .
            DIRECTORY_SEPARATOR .
            DIRNAME_EXPRESS_FORM_CONTROLS .
            DIRECTORY_SEPARATOR .
            DIRNAME_EXPRESS_FORM_CONTROLS // not a typo
        );
        return $locator;
    }

}
