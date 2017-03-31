<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Filesystem\TemplateLocator;

class ViewContext extends Context
{


    public function render(Key $key, $value = null)
    {
        echo $value->getDisplayValue();
    }

}
