<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Style\StyleInterface;
use Concrete\Core\StyleCustomizer\Style\TextTransformStyle;

class TextTransformParser extends AbstractParser
{

    public function createStyleObject(): StyleInterface
    {
        return new TextTransformStyle();
    }

}