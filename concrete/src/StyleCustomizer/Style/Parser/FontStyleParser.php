<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Style\FontStyleStyle;
use Concrete\Core\StyleCustomizer\Style\StyleInterface;

class FontStyleParser extends AbstractParser
{

    public function createStyleObject(): StyleInterface
    {
        return new FontStyleStyle();
    }

}