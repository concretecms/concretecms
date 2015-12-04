<?php

namespace Concrete\Core\Express\Form\Control;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\RendererFactory;
use Concrete\Core\Foundation\Environment;

abstract class EntityPropertyControlRenderer implements RendererInterface
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