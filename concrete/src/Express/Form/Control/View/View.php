<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Form\Control\FormView as BaseFormView;

abstract class View extends BaseFormView
{

    public function __construct(ContextInterface $context, Control $control)
    {
        parent::__construct($context);
        $this->control = $control;
        $this->addScopeItem('control', $control);
        $this->addScopeItem('label', $control->getDisplayLabel());
        $this->addScopeItem('entry', $context->getEntry());
    }


}
