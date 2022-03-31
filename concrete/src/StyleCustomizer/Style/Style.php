<?php

namespace Concrete\Core\StyleCustomizer\Style;

/**
 * @method static \Concrete\Core\StyleCustomizer\Style\Value\Value[] getValuesFromVariables($rules = [])
 */
abstract class Style implements StyleInterface
{
    /**
     * The name of this style.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The name of the associated CSS variable.
     *
     * @var string
     */
    protected $variable = '';

    /**
     * Get the type handle of a given Style instance.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Style|object $class
     * @param string $suffix
     *
     * @return string
     */
    protected static function getTypeFromClass($class, $suffix = 'Style')
    {
        $class = get_class($class);
        $class = substr($class, strrpos($class, '\\') + 1);
        $type = uncamelcase(substr($class, 0, strrpos($class, $suffix)));
        return $type;
    }

    /**
     * Set the name of this style.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * Get the name of this style.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the display name for this style (localized and escaped accordingly to $format).
     *
     * @param string $format = 'html'
     *   Escape the result in html format (if $format is 'html').
     *   If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getDisplayName($format = 'html')
    {
        $value = tc('StyleName', $this->getName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * Set the name of the associated CSS variable.
     *
     * @param string $variable
     *
     * @return $this
     */
    public function setVariable($variable)
    {
        $this->variable = (string) $variable;

        return $this;
    }

    /**
     * Get the name of the associated CSS variable.
     *
     * @return string
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * Gets the name of the CSS variable we should use to inspect the data collection AND
     * write into the request data collection. The reason for this is because the new customizer
     * just has a unified approach to this –whereas the old customizer made you define the name of the
     * variable in the xml file as `page-background` and reference it with `page-background-color` in the
     * variable collections.
     */
    public function getVariableToInspect()
    {
        return $this->getVariable();
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'name' => $this->getDisplayName('text'),
            'type' => self::getTypeFromClass($this),
            'variable' => $this->getVariable(),
        ];
    }
}
