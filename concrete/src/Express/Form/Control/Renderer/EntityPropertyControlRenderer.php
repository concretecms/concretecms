<?php
namespace Concrete\Core\Express\Form\Control\Renderer;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\Control\Template\Template;
use Concrete\Core\Express\Form\RendererFactory;

abstract class EntityPropertyControlRenderer extends AbstractControlRenderer
{

    abstract public function getTemplateHandle();

    public function render(ContextInterface $context, Control $control, Entry $entry = null)
    {
        $template = new Template($this->getTemplateHandle());
        $view = new EntityPropertyControlView($context);
        return $view->render($control, $context->getTemplateFile($template));
    }
}
