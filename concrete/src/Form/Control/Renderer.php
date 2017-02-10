<?php
namespace Concrete\Core\Form\Control;

use Concrete\Core\Form\Context\ContextInterface;

final class Renderer implements RendererInterface
{

    protected $view;
    protected $context;

    public function __construct(ViewInterface $view, ContextInterface $context)
    {
        $this->view = $view;
        $this->context = $context;
    }

    public function render()
    {
        $locator = $this->view->createTemplateLocator();
        $locator = $this->context->setLocation($locator);

        // Put items in the local scope
        $view = $this->view;
        $context = $this->context;
        extract($this->view->getScopeItems());

        if (!($file = $locator->getFile())) {
            throw new \Exception(t('The template locator for this control has no file.'));
        }

        include($file);

    }


}
