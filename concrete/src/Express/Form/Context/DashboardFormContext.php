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
            '/' .
            DIRNAME_EXPRESS .
            '/' .
            DIRNAME_EXPRESS_FORM_CONTROLS .
            '/' .
            DIRNAME_EXPRESS_FORM_CONTROLS . // not a typo
            '/' .
            DIRNAME_DASHBOARD
        );
        return $locator;
    }




}
