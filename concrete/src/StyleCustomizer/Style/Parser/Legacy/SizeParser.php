<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser\Legacy;

use Concrete\Core\StyleCustomizer\Style\Legacy\SizeStyle;
use Concrete\Core\StyleCustomizer\Style\Parser\AbstractParser;
use Concrete\Core\StyleCustomizer\Style\StyleInterface;

class SizeParser extends AbstractParser
{

    public function createStyleObject(): StyleInterface
    {
        return new SizeStyle();
    }


}