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

class ViewContext implements ContextInterface
{

    protected $entry;
    protected $form;

    /**
     * @return mixed
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param mixed $entry
     */
    public function setEntry(Entry $entry)
    {
        $this->entry = $entry;
    }

    public function getAttributeContext()
    {
        return new BasicFormViewContext();
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $form
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

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
