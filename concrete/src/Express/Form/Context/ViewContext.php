<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Attribute\Context\BasicFormContext;
use Concrete\Core\Attribute\Context\BasicFormViewContext;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Group\FormView;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Form\Control\ControlInterface;

class ViewContext implements ContextInterface
{

    protected $entry;

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

    public function setLocation(TemplateLocator $locator)
    {
        $locator->addLocation(DIRNAME_ELEMENTS .
            DIRECTORY_SEPARATOR .
            DIRNAME_EXPRESS .
            DIRECTORY_SEPARATOR .
            DIRNAME_EXPRESS_FORM_CONTROLS .
            DIRECTORY_SEPARATOR .
            DIRNAME_EXPRESS_VIEW_CONTROLS);
        return $locator;
    }


}
