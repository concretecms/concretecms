<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Form\Group\ControlView;

class AttributeKeyFormView extends ControlView
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

    public function createTemplateLocator()
    {
        return $this->view->createTemplateLocator();
    }

    public function getView()
    {
        return $this->view;
    }


}
