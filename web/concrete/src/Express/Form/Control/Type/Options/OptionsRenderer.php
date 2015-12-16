<?php

namespace Concrete\Core\Express\Form\Control\Type\Options;

use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\RendererFactory;

class OptionsRenderer implements RendererInterface
{

    protected $application;
    protected $factory;

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
            '/' . DIRNAME_EXPRESS_CONTROL_OPTIONS .
            '/' . FILENAME_EXPRESS_CONTROL_OPTIONS
        );

        $view->addScopeItem('control', $this->factory->getControl());
        return $view->render($template);
    }


}