<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Attribute\Form\Group\ComposerView;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Form\Control\ControlInterface;

class ComposerContext extends BasicFormContext
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

    public function setLocation(TemplateLocator $locator)
    {
        $locator->setTemplate('composer');
        return $locator;
    }


}
