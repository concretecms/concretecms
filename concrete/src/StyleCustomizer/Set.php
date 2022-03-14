<?php

namespace Concrete\Core\StyleCustomizer;

use Concrete\Core\StyleCustomizer\Style\Style;

class Set implements \JsonSerializable
{
    /**
     * The name of the style customizer set.
     *
     * @var string
     */
    protected $name;

    /**
     * The list of styles associated to this set.
     *
     * @var \Concrete\Core\StyleCustomizer\Style\Style[]
     */
    protected $styles = [];

    /**
     * Set the name of the style customizer set.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the name of the style customizer set.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the display name for this style set (localized and escaped accordingly to $format).
     *
     * @param string $format = 'html'
     *   Escape the result in html format (if $format is 'html').
     *   If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getDisplayName($format = 'html')
    {
        $value = tc('StyleSetName', $this->getName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * Add a style to this set.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Style $style
     */
    public function addStyle(Style $style)
    {
        $this->styles[] = $style;
    }

    /**
     * Get the list of styles associated to this set.
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Style[]
     */
    public function getStyles()
    {
        return $this->styles;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'name' => $this->getDisplayName('text'),
            'styles' => $this->getStyles(),
        ];
    }
}
