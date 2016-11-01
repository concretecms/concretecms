<?php
namespace Concrete\Core\Attribute\Context;

class StandardFormContext extends BasicFormContext
{

    public function __construct()
    {
        parent::__construct();
        $this->preferActionIfAvailable('composer');
        $this->preferTemplateIfAvailable('composer');
    }

}
