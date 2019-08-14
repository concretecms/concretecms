<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Entity\Express\Control\TextControl;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Form\Context\ContextInterface;

/**
 * @since 8.2.0
 */
class TextView extends View
{

    /**
     * @since 8.4.0
     */
    public function getControlID()
    {
        return null;
    }

    public function createTemplateLocator()
    {
        $locator = new TemplateLocator('text');
        return $locator;
    }


}
