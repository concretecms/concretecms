<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\Form\Context\ContextInterface;

class Renderer
{

    protected $context;
    protected $form;

    public function __construct(ContextInterface $context, Form $form)
    {
        $this->context = $context;
        $this->form = $form;
    }

    /**
     * @return ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param ContextInterface $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }


    public function render(Entry $entry = null)
    {
        if ($entry) {
            $this->context->setEntry($entry);
        }

        $view = $this->form->getControlView($this->context);
        $renderer = $view->getControlRenderer();
        $renderer->render();
    }

}
