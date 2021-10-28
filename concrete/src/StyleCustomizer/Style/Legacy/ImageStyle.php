<?php

namespace Concrete\Core\StyleCustomizer\Style\Legacy;

use Concrete\Core\StyleCustomizer\Normalizer\Legacy\ImageVariable;
use Concrete\Core\StyleCustomizer\Style\ImageStyle as BaseStyle;

class ImageStyle extends BaseStyle
{

    public function createImageVariable($variable, $url)
    {
        return new ImageVariable($variable, $url);
    }

    public function getVariableToInspect()
    {
        return $this->getVariable() . '-image';
    }

}
