<?php

namespace Concrete\Core\StyleCustomizer\Style\Legacy;

use Concrete\Core\StyleCustomizer\Style\ImageStyle as BaseStyle;

class ImageStyle extends BaseStyle
{

    public function getVariableToInspect()
    {
        return $this->getVariable() . '-image';
    }

}
