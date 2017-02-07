<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Attribute\Form\Group\StandardView;
use Concrete\Core\Form\Control\ControlInterface;

class BasicSearchContext extends Context
{

    public function getFormGroupView(ControlInterface $control)
    {
        return new StandardView($control, $this);
    }

    public function __construct()
    {
        $this->runActionIfAvailable('search');
        $this->includeTemplateIfAvailable('search');
        $this->includeTemplateIfAvailable('form');
    }

}
