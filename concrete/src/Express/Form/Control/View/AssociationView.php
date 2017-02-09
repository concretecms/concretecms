<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Express\Form\Context\ViewContext;
use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Filesystem\TemplateLocator;

class AssociationView extends View
{

    protected $association;

    public function __construct(ContextInterface $context, Control $control)
    {
        parent::__construct($context, $control);
        $this->association = $this->control->getAssociation();
        $entity = $this->association->getTargetEntity();
        $list = new EntryList($entity);

        $entities = $list->getResults();

        if (is_object($entry)) {
            $related = $entry->getAssociations();
            foreach($related as $relatedAssociation) {
                if ($relatedAssociation->getAssociation()->getID() == $this->association->getID()) {
                    $this->addScopeItem('selectedEntities', $relatedAssociation->getSelectedEntries());
                }
            }
        } else {
            // Is this an owned entity? In which case we get the association from the owning entity
            /*$renderer = $context->getFormRenderer();
            $form = $renderer->getForm();
            if ($form instanceof OwnedEntityForm) {
                $this->addScopeItem('selectedEntities', array($form->getOwningEntry()));
            }
            */
        }

        $this->addScopeItem('entities', $entities);
        $this->addScopeItem('control', $control);
        $this->addScopeItem('formatter', $this->association->getFormatter());
    }

    /**
     * @param AssociationControl $control
     * @return string
     */
    protected function getFormFieldElement(AssociationControl $control)
    {
        $class = get_class($control->getAssociation());
        $class = strtolower(str_replace(array('Concrete\\Core\\Entity\\Express\\', 'Association'), '', $class));
        if (substr($class, -4) == 'many') {
            return 'select_multiple';
        } else {
            return 'select';
        }
    }

    public function createTemplateLocator()
    {
        // Is this an owning entity with display order? If so, we render a separate reordering control
        $element = $this->getFormFieldElement($this->control);
        $association = $this->association;
        if ($association->isOwningAssociation()) {
            if ($association->getTargetEntity()->supportsCustomDisplayOrder()) {
                $element = 'select_multiple_reorder';
            } else {
                return false;
            }
        }

        if ($this->context instanceof ViewContext) {
            $locator = new TemplateLocator('association');
        } else {
            $locator = new TemplateLocator('association/' . $element);
        }
        return $locator;
    }



}
