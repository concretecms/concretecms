<?php
namespace Concrete\Core\Express\Form\Control\Form;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\RendererFactory;

class AssociationControlFormRenderer implements RendererInterface
{
    protected $application;
    protected $factory;
    protected $entry;

    public function __construct(Entry $entry = null)
    {
        $this->entry = $entry;
    }

    public function build(RendererFactory $factory)
    {
        $this->factory = $factory;
        $this->application = $factory->getApplication();
    }

    protected function getFormFieldElement()
    {
        $class = get_class($this->factory->getControl()->getAssociation());
        $class = strtolower(str_replace(array('Concrete\\Core\\Entity\\Express\\', 'Association'), '', $class));
        if (substr($class, -4) == 'many') {
            return 'select_multiple';
        } else {
            return 'select';
        }
    }

    public function render()
    {
        $template = $this->application->make('environment')->getPath(
            DIRNAME_ELEMENTS .
            '/' . DIRNAME_EXPRESS .
            '/' . DIRNAME_EXPRESS_FORM_CONTROLS .
            '/' . DIRNAME_EXPRESS_FORM_CONTROLS_ASSOCIATION .
            '/' . $this->getFormFieldElement() . '.php'
        );
        $association = $this->factory->getControl()->getAssociation();
        $entity = $this->factory->getControl()->getAssociation()->getTargetEntity();
        $list = new EntryList($entity);
        $entities = $list->getResults();
        $view = new EntityPropertyControlView($this->factory);

        if (is_object($this->entry)) {
            $related = $this->entry->getAssociations();
            foreach($related as $relatedAssociation) {
                if ($relatedAssociation->getAssociation()->getID() == $association->getID()) {
                    $view->addScopeItem('selectedEntities', $relatedAssociation->getSelectedEntries());
                }
            }
        }

        $view->addScopeItem('entities', $entities);
        $view->addScopeItem('control', $this->factory->getControl());
        $view->addScopeItem('formatter', $association->getFormatter());

        return $view->render($template);
    }
}
