<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Form\Control\FormView as BaseFormView;

/**
 * @since 8.2.0
 */
class AttributeKeyFormView extends BaseFormView
{

    protected $key;
    protected $view;

    /**
     * AttributeKeyView constructor.
     * @param ContextInterface $context
     * @param AttributeKeyControl $control
     */
    public function __construct(ContextInterface $context, Control $control)
    {
        $key = $control->getAttributeKey();
        parent::__construct($context);
        $entry = $context->getEntry();

        $this->context = $context->getAttributeContext();
        $this->key = $key;
        $this->view = $this->key->getController()->getControlView($this->context);
        $this->view->setIsRequired($control->isRequired());
        $this->view->setLabel($control->getDisplayLabel());
        if (is_object($entry)) {
            $this->view->setValue($entry->getAttributeValueObject($key));
        }
    }

    /**
     * @since 8.4.0
     */
    public function getControlID()
    {
        return $this->key->getController()->getControlID();
    }

    public function createTemplateLocator()
    {
        return $this->view->createTemplateLocator();
    }

    public function getView()
    {
        return $this->view;
    }


}
