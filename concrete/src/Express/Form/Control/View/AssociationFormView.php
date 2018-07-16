<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Filesystem\TemplateLocator;

class AssociationFormView extends AssociationView
{

    protected $association;

    public function __construct(ContextInterface $context, Control $control)
    {
        parent::__construct($context, $control);
        $this->addScopeItem('entities', $this->allEntities);
        $this->addScopeItem('selectedEntities', $this->selectedEntities);
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
                $element = 'view';
            }
        }
        $locator = new TemplateLocator('association/' . $element);
        return $locator;
    }



}
