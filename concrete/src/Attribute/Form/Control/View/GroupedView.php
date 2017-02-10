<?php
namespace Concrete\Core\Attribute\Form\Control\View;

class GroupedView extends View
{

    public function createTemplateLocator()
    {
        $locator = parent::createTemplateLocator();
        $locator->prependLocation(DIRNAME_ELEMENTS . DIRECTORY_SEPARATOR . DIRNAME_FORM_CONTROL_WRAPPER_TEMPLATES .
            DIRECTORY_SEPARATOR . 'grouped');
        return $locator;
    }

}
