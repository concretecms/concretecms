<?php
namespace Concrete\Core\Attribute\Context;

class BasicFormContext extends FormContext
{

    public function __construct()
    {
        $this->runActionIfAvailable('form');
        $this->includeTemplateIfAvailable('form');
    }

}
