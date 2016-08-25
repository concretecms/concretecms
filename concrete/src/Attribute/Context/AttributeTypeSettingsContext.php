<?php
namespace Concrete\Core\Attribute\Context;

class AttributeTypeSettingsContext extends Context
{

    public function __construct()
    {
        $this->runActionIfAvailable('type_form');
        $this->includeTemplateIfAvailable('type_form');
    }

}
