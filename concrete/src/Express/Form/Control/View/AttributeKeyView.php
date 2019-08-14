<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Express\Form\Context\ContextInterface;

/**
 * @since 8.2.0
 */
class AttributeKeyView extends View
{

    /**
     * AttributeKeyView constructor.
     * @param ContextInterface $context
     * @param AttributeKeyControl $control
     */
    public function __construct(ContextInterface $context, Control $control)
    {
        parent::__construct($context, $control);
        if ($entry = $context->getEntry()) {
            $this->addScopeItem('value', $entry->getAttributeValueObject($control->getAttributeKey()));
        }
    }

    /**
     * @since 8.4.0
     */
    public function getControlID()
    {
        return null;
    }

    public function createTemplateLocator()
    {
        $locator = new TemplateLocator('attribute_key');
        return $locator;
    }


}
