<?php
namespace Concrete\Core\Express\Form\Control\Renderer;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\Control\Template\Template;
use Concrete\Core\Express\Form\OwnedEntityForm;
use Concrete\Core\Express\Form\RendererFactory;

class AssociationControlFormRenderer extends AbstractControlRenderer
{

    /**
     * @param AssociationControl $control
     * @return string
     */
    protected function getFormFieldElement(Control $control)
    {
        $class = get_class($control->getAssociation());
        $class = strtolower(str_replace(array('Concrete\\Core\\Entity\\Express\\', 'Association'), '', $class));
        if (substr($class, -4) == 'many') {
            return 'select_multiple';
        } else {
            return 'select';
        }
    }

    /**
     * @param ContextInterface $context
     * @param AssociationControl $control
     * @return string
     */
    public function render(ContextInterface $context, Control $control, Entry $entry = null)
    {

        // Is this an owning entity? If so, we don't render anything
        // because the entity is edited separately
        if ($control->getAssociation()->isOwningAssociation()) {
            return;
        }

        $template = new Template('association/' . $this->getFormFieldElement($control));

        $association = $control->getAssociation();
        $entity = $control->getAssociation()->getTargetEntity();
        $list = new EntryList($entity);
        $entities = $list->getResults();
        $view = new EntityPropertyControlView($context);

        if (is_object($entry)) {
            $related = $entry->getAssociations();
            foreach($related as $relatedAssociation) {
                if ($relatedAssociation->getAssociation()->getID() == $association->getID()) {
                    $view->addScopeItem('selectedEntities', $relatedAssociation->getSelectedEntries());
                }
            }
        } else {
            // Is this an owned entity? In which case we get the association from the owning entity
            $renderer = $context->getFormRenderer();
            $form = $renderer->getForm();
            if ($form instanceof OwnedEntityForm) {
                $view->addScopeItem('selectedEntities', array($form->getOwningEntry()));
            }
        }

        $view->addScopeItem('entities', $entities);
        $view->addScopeItem('control', $control);
        $view->addScopeItem('formatter', $association->getFormatter());

        return $view->render($control, $context->getTemplateLocator($template)->getFile());
    }
}
