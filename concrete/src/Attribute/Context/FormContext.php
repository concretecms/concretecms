<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Attribute\Form\Control\StandardView;

class FormContext extends Context implements FormContextInterface
{

    public function getFormControlView()
    {
        return new StandardView($this);
    }




}
