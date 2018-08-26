<?php

namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Context\ContextInterface;

class Renderer
{
    /**
     * @var \Concrete\Core\Express\Form\Context\ContextInterface
     */
    protected $context;

    /**
     * The form that's going to be rendered.
     *
     * @var \Concrete\Core\Express\Form\FormInterface
     */
    protected $form;

    public function __construct(ContextInterface $context, FormInterface $form)
    {
        $this->context = $context;
        $this->form = $form;
        $this->context->setForm($form);
    }

    /**
     * @return \Concrete\Core\Express\Form\Context\ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param \Concrete\Core\Express\Form\Context\ContextInterface $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @param \Concrete\Core\Entity\Express\Entry|null $entry
     *
     * @throws \Exception
     */
    public function render(Entry $entry = null)
    {
        if ($entry) {
            $this->context->setEntry($entry);
        }

        /** @var \Concrete\Core\Form\Control\View $view */
        $view = $this->form->getControlView($this->context);

        $renderer = $view->getControlRenderer();
        $renderer->render();
    }
}
