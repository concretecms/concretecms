<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Filesystem\TemplateLocator;

class DashboardViewContext extends ViewContext
{

    public function setLocation(TemplateLocator $locator)
    {
        $locator = parent::setLocation($locator);
        $locator->prependLocation(DIRNAME_ELEMENTS .
            '/' .
            DIRNAME_EXPRESS .
            '/' .
            DIRNAME_EXPRESS_FORM_CONTROLS .
            '/' .
            DIRNAME_EXPRESS_VIEW_CONTROLS .
            '/' .
            DIRNAME_DASHBOARD
        );
        return $locator;
    }


}
