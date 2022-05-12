<?php

namespace Concrete\Core\StyleCustomizer\Style;

class GroupedStyleValueListSet implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var StyleValue[]
     */
    protected $values;

    /**
     * GroupedStyleValueListSet constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
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
     */
    public function getDisplayName(string $format = 'html'): string
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
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['name' => $this->getDisplayName('text'), 'styles' => $this->values];
    }

    public function addValue(StyleValue $value)
    {
        $this->values[] = $value;
    }

}
