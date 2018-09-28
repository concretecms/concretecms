<?php
namespace Concrete\Core\Attribute\Form\Control\View;

class GroupedView extends View
{

    public function createTemplateLocator()
    {
        $locator = parent::createTemplateLocator();
        $locator->prependLocation(DIRNAME_ELEMENTS . '/' . DIRNAME_FORM_CONTROL_WRAPPER_TEMPLATES . '/grouped');
        return $locator;
    }

}
