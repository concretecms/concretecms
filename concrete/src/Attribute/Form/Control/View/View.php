<?php
namespace Concrete\Core\Attribute\Form\Control\View;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Attribute\Context\ContextInterface as AttributeContextInterface;
use Concrete\Core\Form\Control\FormView as BaseFormView;

class View extends BaseFormView
{

    protected $controller;
    protected $value;
    protected $key;

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


    public function __construct(ContextInterface $context, Key $key, AbstractValue $value = null)
    {
        parent::__construct($context);
        $this->key = $key;
        $this->value = $value;
        $this->setLabel($key->getAttributekeyDisplayName());
        $this->addScopeItem('key', $key);
    }

    public function createTemplateLocator()
    {
        $locator = new TemplateLocator();
        $locator->addLocation(DIRNAME_ELEMENTS . '/' . DIRNAME_FORM_CONTROL_WRAPPER_TEMPLATES);
        return $locator;
    }

    public function renderControl()
    {
        echo $this->context->render($this->key, $this->value);
    }


}
