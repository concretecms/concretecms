<?php

namespace Concrete\Core\Express\Form\Control\Form;

use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\RendererFactory;

class AssociationControlFormRenderer implements RendererInterface
{

    protected $application;
    protected $factory;

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

        $name = $this->factory->getControl()->getAssociation()->getTargetEntity()->getName();
        $className = '\\' . $this->application['config']->get('express.entity_classes.namespace') . '\\' . $name;
        $repository = $this->factory->getEntityManager()->getRepository($className);
        $entities = $repository->findAll();
        $view = new EntityPropertyControlView($this->factory);
        $view->addScopeItem('entities', $entities);
        $view->addScopeItem('control', $this->factory->getControl());
        return $view->render($template);
    }


}