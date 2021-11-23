<?php

namespace Concrete\Core\StyleCustomizer\Style\Legacy;

use Concrete\Core\StyleCustomizer\Style\TypeStyle as BaseStyle;

class TypeStyle extends BaseStyle
{

    public function getVariableToInspect()
    {
        return $this->getVariable() . '-type';
    }

}
