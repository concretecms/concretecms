<?php

namespace Concrete\Core\StyleCustomizer\Style\Legacy;

use Concrete\Core\StyleCustomizer\Style\SizeStyle as BaseStyle;

class SizeStyle extends BaseStyle
{

    public function getVariableToInspect()
    {
        return $this->getVariable() . '-size';
    }

}
