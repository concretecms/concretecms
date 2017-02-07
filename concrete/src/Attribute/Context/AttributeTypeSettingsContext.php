<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Attribute\Form\Group\StandardView;
use Concrete\Core\Form\Control\ControlInterface;

class AttributeTypeSettingsContext extends Context
{

    public function getFormGroupView(ControlInterface $control)
    {
        return null;
    }

    public function __construct()
    {
        $this->runActionIfAvailable('type_form');
        $this->includeTemplateIfAvailable('type_form');
    }

}
