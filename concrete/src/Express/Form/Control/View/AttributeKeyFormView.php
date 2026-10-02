<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Form\Control\FormView as BaseFormView;

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
        $view = $this->key->getController()->getControlView($this->context);
        if (!method_exists($view, 'setIsRequired')) {
            throw new \RuntimeException(t('The control view must have the %s method.', 'setIsRequired()'));
        }
        $this->view = $view;
        $view->setIsRequired($control->isRequired());
        $view->setLabel($control->getDisplayLabel());
        if (is_object($entry)) {
            if (!method_exists($view, 'setValue')) {
                throw new \RuntimeException(t('The control view must have the %s method.', 'setValue()'));
            }
            $view->setValue($entry->getAttributeValueObject($key));
        }
    }

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
