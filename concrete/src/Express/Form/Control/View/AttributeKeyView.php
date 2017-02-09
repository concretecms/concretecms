<?php
namespace Concrete\Core\Express\Form\Control\View;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Form\Group\ControlView;

class AttributeKeyView extends ControlView
{

    protected $key;
    protected $view;

    public function __construct(ContextInterface $context, Key $key)
    {
        parent::__construct($context);

        $this->context = $context->getAttributeContext();
        $this->key = $key;
        $this->view = $this->key->getController()->getControlView($this->context);
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
