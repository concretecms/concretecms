<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class ColorValue extends Value {

    protected $r;
    protected $g;
    protected $b;
    protected $a = false;

    public function setRed($r) {
        $this->r = $r;
    }

    public function setGreen($g) {
        $this->g = $g;
    }

    public function setBlue($b) {
        $this->b = $b;
    }

    public function setAlpha($a) {
        $this->a = $a;
    }

    public function getRed() {return $this->r;}
    public function getGreen() {return $this->g;}
    public function getBlue() {return $this->b;}
    public function getAlpha() {return $this->a;}
    public function hasAlpha() {return $this->a !== false;}

    public function toStyleString()
    {
        if ($this->hasAlpha()) {
            return sprintf('rgba(%s, %s, %s, %s)', $this->r, $this->g, $this->b, $this->a);
        } else {
            return sprintf('rgb(%s, %s, %s)', $this->r, $this->g, $this->b);
        }
    }

    public function toLessVariablesArray()
    {
        return array($this->getVariable() . '-color' => $this->toStyleString());
    }

}