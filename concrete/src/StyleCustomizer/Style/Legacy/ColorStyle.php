<?php

namespace Concrete\Core\StyleCustomizer\Style\Legacy;

use Concrete\Core\StyleCustomizer\Style\ColorStyle as BaseColorStyle;

class ColorStyle extends BaseColorStyle
{

    public function getVariableToInspect()
    {
        return $this->getVariable() . '-color';
    }

}
