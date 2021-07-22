<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Concrete\Core\StyleCustomizer\Style\Style;
use Concrete\Core\StyleCustomizer\Style\TextDecorationStyle;

class TextDecorationParser implements ParserInterface
{

    public function parseNode(\SimpleXMLElement $element, SkinInterface $skin): Style
    {
        $style = new TextDecorationStyle();
        $style->setName((string) $element['name']);
        $style->setVariable((string) $element['variable']);
        return $style;
    }

}