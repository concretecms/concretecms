<?php

namespace Concrete\Core\Express\Form\Control\Form;

use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\RendererFactory;

abstract class EntityPropertyControlFormRenderer implements RendererInterface
{

    protected $factory;

    abstract public function getControlHandle();

    public function build(RendererFactory $factory)
    {
        $this->factory = $factory;
    }

    public function render()
    {
        $template = $this->factory->getApplication()->make('environment')->getPath(
            DIRNAME_ELEMENTS .
            '/' . DIRNAME_EXPRESS .
            '/' . DIRNAME_EXPRESS_FORM_CONTROLS .
            '/' . $this->getControlHandle() . '.php'
        );
        $view = new EntityPropertyControlView($this->factory);
        return $view->render($template);
    }


}