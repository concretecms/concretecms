<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\OwnedEntityForm;
use Concrete\Core\Filesystem\TemplateLocator;

class AssociationView extends View
{
    protected $association;
    protected $entry;
    protected $allEntries = [];
    protected $selectedEntries = [];

    public function getControlID()
    {
        return 'express_association_' . $this->control->getID();
    }

    public function __construct(ContextInterface $context, Control $control)
    {
        parent::__construct($context, $control);
        $this->entry = $context->getEntry();
        $this->association = $this->control->getAssociation();
        /**
         * @var $entity Entity
         */
        $entity = $this->association->getTargetEntity();
        if (AssociationControl::TYPE_ENTRY_SELECTOR != $control->getEntrySelectorMode()) {
            $list = new EntryList($entity);
            if ($entity->usesSeparateSiteResultsBuckets()) {
                $list->filterBySite(app('site')->getActiveSiteForEditing());
            }
            $this->allEntries = $list->getResults();
        }

        if (is_object($this->entry)) {
            $related = $this->entry->getAssociations();
            foreach ($related as $relatedAssociation) {
                if ($relatedAssociation->getAssociation()->getID() == $this->association->getID()) {
                    $this->selectedEntries = $relatedAssociation->getSelectedEntries();
                }
            }
        } else {
            $form = $context->getForm();
            if ($form instanceof OwnedEntityForm) {
                $this->selectedEntries = [$form->getOwningEntry()];
            }
        }

        $this->addScopeItem('selectedEntries', $this->selectedEntries);
        $this->addScopeItem('control', $control);
        $this->addScopeItem('association', $this->association);
        $this->addScopeItem('formatter', $this->association->getFormatter());

        // @deprecated - use selectedEntries instead
        $this->addScopeItem('entities', $this->selectedEntries);

    }

    public function createTemplateLocator()
    {
        $locator = new TemplateLocator('association');

        return $locator;
    }
}
