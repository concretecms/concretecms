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
     * @since 8.2.0
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }

    /**
     * @param mixed $tooltip
     * @since 8.2.0
     */
    public function setTooltip($tooltip)
    {
        $this->tooltip = $tooltip;
    }

    /**
     * @return bool
     * @since 8.2.0
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     * @since 8.2.0
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @since 8.2.0
     */
    public function setLocation(TemplateLocator $locator)
    {
        $locator->setTemplate('composer');
        return $locator;
    }


}
