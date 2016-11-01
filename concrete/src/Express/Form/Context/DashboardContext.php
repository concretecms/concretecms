<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Express\Form\Control\Template\Template;

class DashboardContext extends ViewContext
{

    // We pass the context from the parent so that
    // we use the standard view context for most items.
    public function getContextHandle()
    {
        return 'view';
    }

    // We add in a custom template locator that looks in dashboard_view
    // first for certain items.
    public function getTemplateLocator(Template $template)
    {
        $locator = parent::getTemplateLocator($template);
        $locator->prependLocation(
            DIRNAME_ELEMENTS
            . DIRECTORY_SEPARATOR
            . DIRNAME_EXPRESS
            . DIRECTORY_SEPARATOR
            . DIRNAME_EXPRESS_FORM_CONTROLS
            . DIRECTORY_SEPARATOR
            . 'dashboard_view'
            . DIRECTORY_SEPARATOR
            . $template->getTemplateHandle() . '.php'
        );
        return $locator;
    }

}
