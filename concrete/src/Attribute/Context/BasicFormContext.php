<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Attribute\View;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Filesystem\TemplateLocator;

class BasicFormContext extends Context
{

    public function __construct()
    {
        $this->runActionIfAvailable('composer'); //legacy
        $this->runActionIfAvailable('form');
        $this->includeTemplateIfAvailable('composer'); //legacy
        $this->includeTemplateIfAvailable('form');
    }

    public function setLocation(TemplateLocator $locator)
    {
        $locator->setTemplate('bootstrap5');
        return $locator;
    }


}
