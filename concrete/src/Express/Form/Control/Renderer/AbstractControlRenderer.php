<?php
namespace Concrete\Core\Express\Form\Control\Renderer;

use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Context\AbstractContext;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\Control\EntityPropertyControlView;
use Concrete\Core\Express\Form\Control\RendererInterface;
use Concrete\Core\Express\Form\Control\Template\Template;
use Concrete\Core\Express\Form\RendererFactory;

abstract class AbstractControlRenderer implements RendererInterface
{

    abstract protected function getTemplateHandle();

    protected function getTemplate(ContextInterface $context, Control $control)
    {
        $template = new Template($context);
        $template->addTemplateSegment($this->getTemplateHandle());
        return $template;
    }

}
