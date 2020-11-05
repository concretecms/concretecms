<?php
namespace Concrete\Core\Attribute\Form\Control\View;

use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Attribute\Context\ContextInterface as AttributeContextInterface;
use Concrete\Core\Form\Control\FormView as BaseFormView;
use Concrete\Core\Attribute\View as AttributeView;

class View extends BaseFormView
{

    protected $controller;
    protected $value;
    protected $key;

    /**
     * @var ObjectInterface
     */
    protected $object;

    /**
     * @var AttributeContextInterface
     */
    protected $context;
    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param ObjectInterface $object
     */
    public function setObject(ObjectInterface $object): void
    {
        $this->object = $object;
    }

    public function __construct(ContextInterface $context, Key $key, AbstractValue $value = null)
    {
        parent::__construct($context);
        $this->key = $key;
        $this->value = $value;
        $this->setLabel($key->getAttributekeyDisplayName());
        $this->addScopeItem('key', $key);
    }

    public function getControlID()
    {
        return $this->key->getController()->getControlID();
    }

    public function createTemplateLocator()
    {
        $locator = new TemplateLocator();
        $locator->addLocation(DIRNAME_ELEMENTS . '/' . DIRNAME_FORM_CONTROL_WRAPPER_TEMPLATES);
        return $locator;
    }

    public function renderControl()
    {
        if (is_object($this->value)) {
            $v = new AttributeView($this->value);
        } else {
            $v = new AttributeView($this->key);
        }
        if (isset($this->object)) {
            $v->setAttributeObject($this->object);
        }
        echo $v->render($this->context);
    }


}
