<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Form\Control\View as AbstractView;
use Concrete\Core\Express\Form\Context\ContextInterface;

class FormView extends AbstractView
{

    public function __construct(ContextInterface $context, Form $form)
    {
        parent::__construct($context);
        $this->addScopeItem('form', $form);
        $this->addScopeItem('token', \Core::make('token'));
        $this->addScopeItem('entry', $context->getEntry());
    }

    public function createTemplateLocator()
    {
        $locator = new TemplateLocator('form');
        return $locator;
    }


}
