<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Attribute\Form\Group\StandardView;
use Concrete\Core\Form\Control\ControlInterface;

class BasicFormContext extends Context
{

    public function getFormGroupView(ControlInterface $control)
    {
        return new StandardView($control, $this);
    }

    public function __construct()
    {
        $this->runActionIfAvailable('composer'); //legacy
        $this->runActionIfAvailable('form');
        $this->includeTemplateIfAvailable('composer'); //legacy
        $this->includeTemplateIfAvailable('form');
    }

}
