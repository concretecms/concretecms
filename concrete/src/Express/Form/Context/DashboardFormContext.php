<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Express\Form\Group\DashboardFormView;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Form\Control\ControlInterface;
use Concrete\Core\Attribute\Context\DashboardFormContext as AttributeDashboardFormContext;

class DashboardFormContext extends FormContext
{

    public function getAttributeContext()
    {
        return new AttributeDashboardFormContext();
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
            DIRNAME_EXPRESS_FORM_CONTROLS . // not a typo
            DIRECTORY_SEPARATOR .
            DIRNAME_DASHBOARD
        );
        return $locator;
    }




}
