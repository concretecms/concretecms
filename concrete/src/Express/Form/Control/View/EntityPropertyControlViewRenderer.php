<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\RendererFactory;

abstract class EntityPropertyControlViewRenderer implements RendererInterface
{
    abstract public function getControlHandle();

    public function render(ContextInterface $context, Control $control, Entry $entry = null)
    {
        $template = $context->getApplication()->make('environment')->getPath(
            DIRNAME_ELEMENTS .
            '/' . DIRNAME_EXPRESS .
            '/' . DIRNAME_EXPRESS_VIEW_CONTROLS .
            '/' . $this->getControlHandle() . '.php'
        );
        $view = new EntityPropertyControlView($context);

        return $view->render($control, $template);
    }
}
