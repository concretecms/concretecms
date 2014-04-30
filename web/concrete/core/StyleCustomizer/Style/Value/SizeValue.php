<?php
namespace Concrete\Core\StyleCustomizer\Style\Value;
class SizeValue extends Value {

    protected $size;
    protected $unit;

    public function __construct($size, $unit = 'px')
    {
        $this->size = $size;
        if ($unit != false) {
            $this->unit = $unit;
        }

    }

    public function getSize() {return $this->size;}
    public function getUnit() {return $this->unit;}
    public function getUnits() {return $this->getUnit();}

    public function toStyleString() {
        return $this->size . $this->unit;
    }

}