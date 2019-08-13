<?php
namespace Concrete\Core\Attribute\Context;

/**
 * @since 8.0.0
 */
class AttributeTypeSettingsContext extends Context
{

    public function __construct()
    {
        $this->runActionIfAvailable('type_form');
        $this->includeTemplateIfAvailable('type_form');
    }

}
