<?php
namespace Concrete\Core\StyleCustomizer\Style\Value;

abstract class Value
{
    abstract public function toStyleString();
    abstract public function toLessVariablesArray();

    protected $variable;
    protected $scvID;
    protected $scvlID;

    public function __construct($variable = false)
    {
        $this->variable = $variable;
    }

    public function getVariable()
    {
        return $this->variable;
    }
}
