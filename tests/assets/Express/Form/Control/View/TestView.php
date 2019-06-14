<?php
namespace Concrete\Tests\Express\Form\Control\View;

use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\Form\Control\View\View;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Filesystem\TemplateLocator;

class TestView extends View
{

    /**
     * AttributeKeyView constructor.
     * @param ContextInterface $context
     * @param AttributeKeyControl $control
     */
    public function __construct(ContextInterface $context, Control $control)
    {
        parent::__construct($context, $control);
    }

    public function getControlID()
    {
        return 'tests';
    }

    public function createTemplateLocator()
    {
        $locator = new TemplateLocator('tests');

        return $locator;
    }
}
