<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Style\FontFamilyStyle;
use Concrete\Core\StyleCustomizer\Style\Style;

class FontFamilyParser implements ParserInterface
{

    public function parseNode(\SimpleXMLElement $element): Style
    {
        $style = new FontFamilyStyle();
        $style->setName((string) $element['name']);
        $style->setVariable((string) $element['variable']);
        return $style;
    }

}