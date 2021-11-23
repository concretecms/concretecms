<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser\Legacy;

use Concrete\Core\StyleCustomizer\Style\Legacy\ImageStyle;
use Concrete\Core\StyleCustomizer\Style\Parser\AbstractParser;
use Concrete\Core\StyleCustomizer\Style\StyleInterface;

class ImageParser extends AbstractParser
{

    public function createStyleObject(): StyleInterface
    {
        return new ImageStyle();
    }


}