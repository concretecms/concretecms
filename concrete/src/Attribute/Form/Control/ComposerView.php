<?php
namespace Concrete\Core\Attribute\Form\Control;

use Concrete\Core\Attribute\Context\ComposerContext;
use Concrete\Core\Attribute\Context\ContextInterface;
use Concrete\Core\Attribute\Key\Key;

class ComposerView extends View
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

    public function __construct(ContextInterface $context)
    {
        parent::__construct($context);
        $this->setFieldWrapperTemplate('composer');
    }

    public function enableGroupedTemplate()
    {
        $this->setFieldWrapperTemplate('composer_grouped');
    }
}
