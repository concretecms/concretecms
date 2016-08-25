<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Entity\Express\Control\Control;

class FormContext extends Context
{

    public function getControlRenderer(Control $control)
    {
        return $control->getFormControlRenderer();
    }


}
