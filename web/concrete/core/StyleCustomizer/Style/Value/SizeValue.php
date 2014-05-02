<?php
namespace Concrete\Core\StyleCustomizer\Style\Value;
class SizeValue extends Value {

    protected $size;
    protected $unit = 'px';

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    public function getSize() {return $this->size;}
    public function getUnit() {return $this->unit;}
    public function getUnits() {return $this->getUnit();}

    public function toStyleString() {
        return $this->size . $this->unit;
    }

    public function toLessVariablesArray()
    {
        return array($this->getVariable() . '-size' => $this->toStyleString());
    }

}