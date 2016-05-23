<?php
namespace Concrete\Core\Service\Rule;

/**
 * Represents an option for a configurable rule.
 */
class Option
{
    /**
     * Option description.
     *
     * @var string
     */
    protected $description;

    /**
     * Is this option required?
     *
     * @var bool|callable
     */
    protected $required;

    /**
     * Option value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Initializes the instance.
     *
     * @param string $description Option description.
     * @param bool|callable $required Is this option required?
     * @param mixed $value Initial rule option.
     */
    public function __construct($description = '', $required = false, $value = null)
    {
        $this->description = (string) $description;
        $this->required = $required;
        $this->value = $value;
    }

    /**
     * Get the option description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Is this option required?
     *
     * @return bool
     */
    public function isRequired()
    {
        $result = $this->required;
        if (is_callable($result)) {
            $result = $result($this);
        }

        return (bool) $result;
    }

    /**
     * Set the option value.
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get the option value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
