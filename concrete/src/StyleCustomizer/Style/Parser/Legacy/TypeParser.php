<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser\Legacy;

use Concrete\Core\StyleCustomizer\Style\Legacy\TypeStyle;
use Concrete\Core\StyleCustomizer\Style\Parser\TypeParser as BaseTypeParser;
use Concrete\Core\StyleCustomizer\Style\StyleInterface;

class TypeParser extends BaseTypeParser
{

    public function createStyleObject(): StyleInterface
    {
        return new TypeStyle();
    }


}