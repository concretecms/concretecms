<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Attribute\View;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Filesystem\TemplateLocator;

/**
 * @since 8.0.0
 */
class BasicFormContext extends Context
{

    public function __construct()
    {
        $this->runActionIfAvailable('composer'); //legacy
        $this->runActionIfAvailable('form');
        $this->includeTemplateIfAvailable('composer'); //legacy
        $this->includeTemplateIfAvailable('form');
    }

    /**
     * @since 8.2.0
     */
    public function setLocation(TemplateLocator $locator)
    {
        $locator->setTemplate('bootstrap3');
        return $locator;
    }

    /**
     * @since 8.2.0
     */
    public function render(Key $key, $value = null)
    {
        if (is_object($value)) {
            $v = new View($value);
        } else {
            $v = new View($key);
        }
        $v->render($this);
    }


}
