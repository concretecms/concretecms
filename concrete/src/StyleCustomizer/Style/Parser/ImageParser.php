<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Style\ImageStyle;
use Concrete\Core\StyleCustomizer\Style\StyleInterface;

class ImageParser extends AbstractParser
{

    public function createStyleObject(): StyleInterface
    {
        return new ImageStyle();
    }


}