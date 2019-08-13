<?php
namespace Concrete\Core\Attribute\Context;

/**
 * @since 8.0.0
 */
class BasicSearchContext extends Context
{

    public function __construct()
    {
        $this->runActionIfAvailable('search');
        $this->includeTemplateIfAvailable('search');
    }

}
