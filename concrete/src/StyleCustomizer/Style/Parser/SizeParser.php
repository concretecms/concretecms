<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Style\SizeStyle;
use Concrete\Core\StyleCustomizer\Style\StyleInterface;

class SizeParser extends AbstractParser
{

    public function createStyleObject(): StyleInterface
    {
        return new SizeStyle();
    }


}