<?php
namespace Concrete\Core\Attribute\Context;

class BasicSearchContext extends Context
{

    public function __construct()
    {
        $this->runActionIfAvailable('search');
        $this->includeTemplateIfAvailable('search');
    }

}
