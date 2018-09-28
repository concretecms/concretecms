<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Form\Context\ContextInterface;

class OwnedEntityForm implements FormInterface
{

    protected $owning_entry;
    protected $form;

    public function __construct(Form $form, Entry $owning_entry)
    {
        $this->form = $form;
        $this->owning_entry = $owning_entry;
    }

    public function getFieldSets()
    {
        return $this->form->getFieldSets();
    }

    /**
     * @return Entry
     */
    public function getOwningEntry()
    {
        return $this->owning_entry;
    }


    public function getId()
    {
        return $this->form->getId();
    }

    public function getControlView(ContextInterface $context)
    {
        return $this->form->getControlView($context);
    }

}
