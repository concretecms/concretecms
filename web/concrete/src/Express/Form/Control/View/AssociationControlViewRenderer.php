<?php

namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Express\BaseEntity;
use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\RendererFactory;

class AssociationControlViewRenderer implements RendererInterface
{

    protected $application;
    protected $entity;
    protected $factory;

    public function __construct(BaseEntity $entity)
    {
        $this->entity = $entity;
    }

    public function build(RendererFactory $factory)
    {
        $this->factory = $factory;
        $this->application = $factory->getApplication();
    }

    public function render()
    {

        $template = $this->application->make('environment')->getPath(
            DIRNAME_ELEMENTS .
            '/' . DIRNAME_EXPRESS .
            '/' . DIRNAME_EXPRESS_VIEW_CONTROLS .
            '/' . DIRNAME_EXPRESS_FORM_CONTROLS_ASSOCIATION .
            '/' . 'list.php'
        );

        $association = $this->factory->getControl()->getAssociation();
        /**
         * @var $association \Concrete\Core\Entity\Express\Association
         */
        $entities = array();
        $entity = $this->entity->get($association->getComputedTargetPropertyName());
        if (is_object($entity)) {
            $entity[] = $entity;
        }
        $view = new EntityPropertyControlView($this->factory);
        $view->addScopeItem('entities', $entities);
        $view->addScopeItem('control', $this->factory->getControl());
        $view->addScopeItem('formatter', $association->getFormatter());
        return $view->render($template);
    }


}