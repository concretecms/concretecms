<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Attribute\Context\BasicFormContext;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\Form\Group\FormView;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Form\Control\ControlInterface;

/**
 * @since 8.0.0
 */
class FormContext extends ViewContext
{

    /**
     * @since 8.2.0
     */
    public function getAttributeContext()
    {
        return new BasicFormContext();
    }

    /**
     * @since 8.2.0
     */
    public function setLocation(TemplateLocator $locator)
    {
        $locator = parent::setLocation($locator);
        $locator->prependLocation(DIRNAME_ELEMENTS .
            '/' .
            DIRNAME_EXPRESS .
            '/' .
            DIRNAME_EXPRESS_FORM_CONTROLS .
            '/' .
            DIRNAME_EXPRESS_FORM_CONTROLS // not a typo
        );
        return $locator;
    }

}
