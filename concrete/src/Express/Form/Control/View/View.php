<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Form\Group\ControlView;

abstract class View extends ControlView
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
