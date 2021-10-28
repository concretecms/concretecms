<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Style\StyleInterface;
use Concrete\Core\StyleCustomizer\Style\TextDecorationStyle;

class TextDecorationParser extends AbstractParser
{

    public function createStyleObject(): StyleInterface
    {
        return new TextDecorationStyle();
    }

}