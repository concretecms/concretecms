<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Filesystem\TemplateLocator;

/**
 * @since 8.0.0
 */
class ComposerContext extends BasicFormContext
{

    /**
     * @since 8.2.0
     */
    protected $tooltip;
    /**
     * @since 8.2.0
     */
    protected $required = false;

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
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    public function setLocation(TemplateLocator $locator)
    {
        $locator->setTemplate('composer');
        return $locator;
    }


}
