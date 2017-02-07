<?php
namespace Concrete\Core\Attribute\Form\Group;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Form\Control\ControlInterface;
use Concrete\Core\Form\Group\View;

class StandardView extends View
{

    /**
     * StandardView constructor.
     * @param Key $control
     * @param ContextInterface $context
     */
    public function __construct(ControlInterface $control, ContextInterface $context)
    {
        parent::__construct($context);
        $this->setFieldWrapperTemplate('bootstrap3');
        $this->setLabel($control->getAttributeKeyDisplayName());
    }

    public function enableGroupedTemplate()
    {
        $this->setFieldWrapperTemplate('bootstrap3_grouped');
    }
}
