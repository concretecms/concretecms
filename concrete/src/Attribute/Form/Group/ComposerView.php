<?php
namespace Concrete\Core\Attribute\Form\Group;

use Concrete\Core\Attribute\Context\ContextInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Form\Control\ControlInterface;

class ComposerView extends StandardView
{

    protected $tooltip;

    /**
     * @return mixed
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }

    /**
     * @param mixed $tooltip
     */
    public function setTooltip($tooltip)
    {
        $this->tooltip = $tooltip;
    }

    /**
     * ComposerView constructor.
     * @param Key $control
     * @param ContextInterface $context
     */
    public function __construct(ControlInterface $control, ContextInterface $context)
    {
        parent::__construct($control, $context);
        $this->setFieldWrapperTemplate('composer');
    }

    public function enableGroupedTemplate()
    {
        $this->setFieldWrapperTemplate('composer_grouped');
    }
}
