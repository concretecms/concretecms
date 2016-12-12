<?php
namespace Concrete\Core\Attribute\Context;

class BasicFormContext extends Context
{

    public function __construct()
    {
        $this->runActionIfAvailable('form');
        $this->includeTemplateIfAvailable('form');
    }

}
