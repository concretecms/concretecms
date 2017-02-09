<?php
namespace Concrete\Core\Attribute\Form\Control;

use Concrete\Core\Attribute\Controller;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Express\Form\Context\ViewContext;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Attribute\Context\ContextInterface as AttributeContextInterface;
use Concrete\Core\Form\Group\ControlViewInterface;
use Concrete\Core\Form\Group\ControlView;

class View extends ControlView
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
    }

    public function createTemplateLocator()
    {
        $locator = new TemplateLocator();
        $locator->addLocation(DIRNAME_ELEMENTS . DIRECTORY_SEPARATOR . DIRNAME_FORM_CONTROL_WRAPPER_TEMPLATES);
        return $locator;
    }

    public function renderControl()
    {
        echo $this->context->render($this->key, $this->value);
    }


}
