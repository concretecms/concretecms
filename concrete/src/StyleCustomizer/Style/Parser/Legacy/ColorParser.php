<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser\Legacy;

use Concrete\Core\StyleCustomizer\Style\Legacy\ColorStyle;
use Concrete\Core\StyleCustomizer\Style\Parser\AbstractParser;
use Concrete\Core\StyleCustomizer\Style\StyleInterface;

class ColorParser extends AbstractParser
{

    public function createStyleObject(): StyleInterface
    {
        return new ColorStyle();
    }


}