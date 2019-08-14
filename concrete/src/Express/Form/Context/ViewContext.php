<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Attribute\Context\BasicFormContext;
use Concrete\Core\Attribute\Context\BasicFormViewContext;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\FormInterface;
use Concrete\Core\Express\Form\Group\FormView;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Form\Control\ControlInterface;

/**
 * @since 8.0.0
 */
class ViewContext implements ContextInterface
{

    /**
     * @since 8.2.0
     */
    protected $entry;
    /**
     * @since 8.2.0
     */
    protected $form;

    /**
     * @return mixed
     * @since 8.2.0
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param mixed $entry
     * @since 8.2.0
     */
    public function setEntry(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @since 8.2.0
     */
    public function getAttributeContext()
    {
        return new BasicFormViewContext();
    }

    /**
     * @return mixed
     * @since 8.2.0
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $form
     * @since 8.2.0
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @since 8.2.0
     */
    public function setLocation(TemplateLocator $locator)
    {
        $locator->addLocation(DIRNAME_ELEMENTS .
            '/' .
            DIRNAME_EXPRESS .
            '/' .
            DIRNAME_EXPRESS_FORM_CONTROLS .
            '/' .
            DIRNAME_EXPRESS_VIEW_CONTROLS);
        return $locator;
    }


}
