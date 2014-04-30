<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class ColorValue extends Value {

    protected $r;
    protected $g;
    protected $b;
    protected $a;
    
    public function __construct($r, $g, $b, $a = false)
    {
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
        if ($a != false) {
            $this->a = $a;
        }

    }

    public function getRed() {return $this->r;}
    public function getGreen() {return $this->g;}
    public function getBlue() {return $this->b;}
    public function getAlpha() {return $this->a;}
    public function hasAlpha() {return $this->a != false;}

}