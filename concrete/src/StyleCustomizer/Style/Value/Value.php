<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

abstract class Value
{
    /**
     * The name of the LESS variable.
     *
     * @var string
     */
    protected $variable;

    /**
     * Initialize the instance.
     *
     * @param string $variable the name of the LESS variable
     */
    public function __construct($variable = '')
    {
        $this->variable = (string) $variable;
    }

    /**
     * Get the name of the LESS variable.
     *
     * @return string
     */
    public function getVariable()
    {
        return (string) $this->variable;
    }

    /**
     * Get the CSS representation of this value.
     *
     * @return string
     */
    abstract public function toStyleString();

    /**
     * Get the LESS representation of this variable and associated values.
     *
     * @return array
     */
    abstract public function toLessVariablesArray();
}
