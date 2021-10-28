<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Style\FontWeightStyle;
use Concrete\Core\StyleCustomizer\Style\StyleInterface;

class FontWeightParser extends AbstractParser
{

    public function createStyleObject(): StyleInterface
    {
        return new FontWeightStyle();
    }

}