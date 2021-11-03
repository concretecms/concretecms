<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Style\ColorStyle;
use Concrete\Core\StyleCustomizer\Style\StyleInterface;

class ColorParser extends AbstractParser
{

    public function createStyleObject(): StyleInterface
    {
        return new ColorStyle();
    }


}