<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\OwnedEntityForm;
use Concrete\Core\Filesystem\TemplateLocator;

class AssociationView extends View
{

    protected $association;
    protected $entry;
    protected $allEntities = [];
    protected $selectedEntities = [];

    public function __construct(ContextInterface $context, Control $control)
    {
        parent::__construct($context, $control);
        $this->entry = $context->getEntry();
        $this->association = $this->control->getAssociation();
        $entity = $this->association->getTargetEntity();
        $list = new EntryList($entity);
        $this->allEntities = $list->getResults();

        if (is_object($this->entry)) {
            $related = $this->entry->getAssociations();
            foreach($related as $relatedAssociation) {
                if ($relatedAssociation->getAssociation()->getID() == $this->association->getID()) {
                    $this->selectedEntities = $relatedAssociation->getSelectedEntries();
                }
            }
        } else {
            $form = $context->getForm();
            if ($form instanceof OwnedEntityForm) {
                $this->selectedEntities = array($form->getOwningEntry());
            }
        }

        $this->addScopeItem('entities', $this->selectedEntities);
        $this->addScopeItem('control', $control);
        $this->addScopeItem('formatter', $this->association->getFormatter());
    }

    public function createTemplateLocator()
    {
        $locator = new TemplateLocator('association');
        return $locator;
    }



}
