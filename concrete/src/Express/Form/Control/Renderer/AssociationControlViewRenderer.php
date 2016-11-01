<?php
namespace Concrete\Core\Express\Form\Control\Renderer;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\Control\Template\Template;
use Concrete\Core\Express\Form\RendererFactory;

class AssociationControlViewRenderer extends AbstractControlRenderer
{

    /**
     * @param ContextInterface $context
     * @param AssociationControl $control
     * @param Entry|null $entry
     * @return string
     */
    public function render(ContextInterface $context, Control $control, Entry $entry = null)
    {
        $template = new Template('association');
        $association = $control->getAssociation();
        /*
         * @var $association \Concrete\Core\Entity\Express\Association
         */
        $related = $entry->getAssociations();
        $view = new EntityPropertyControlView($context);
        foreach($related as $relatedAssociation) {
            if ($relatedAssociation->getAssociation()->getID() == $association->getID()) {
                $view->addScopeItem('entities', $relatedAssociation->getSelectedEntries());
            }
        }
        $view->addScopeItem('control', $control);
        $view->addScopeItem('formatter', $association->getFormatter());

        return $view->render($control, $context->getTemplateLocator($template)->getFile());
    }
}
