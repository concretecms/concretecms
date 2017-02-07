<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Attribute\Form\FormView;

class FormContext extends Context implements FormContextInterface
{

    public function getFormView()
    {
        return new FormView($this);
    }


}
