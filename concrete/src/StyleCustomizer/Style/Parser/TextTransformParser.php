<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Style\Style;
use Concrete\Core\StyleCustomizer\Style\TextTransformStyle;

class TextTransformParser implements ParserInterface
{

    public function parseNode(\SimpleXMLElement $element): Style
    {
        $style = new TextTransformStyle();
        $style->setName((string) $element['name']);
        $style->setVariable((string) $element['variable']);
        return $style;
    }

}