<?php
namespace Concrete\Core\StyleCustomizer;

class Set
{
    protected $name;
    protected $elements = array();

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /** Returns the display name for this style set (localized and escaped accordingly to $format)
     * @param string $format = 'html'
     *   Escape the result in html format (if $format is 'html').
     *   If $format is 'text' or any other value, the display name won't be escaped.
     * @return string
     */
    public function getDisplayName($format = 'html')
    {
        $value = tc('StyleSetName', $this->getName());
        switch($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function addStyle(\Concrete\Core\StyleCustomizer\Style\Style $style)
    {
        $this->styles[] = $style;
    }

    public function getStyles()
    {
        return $this->styles;
    }
}
