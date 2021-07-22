<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Concrete\Core\StyleCustomizer\Style\ColorStyle;
use Concrete\Core\StyleCustomizer\Style\Style;

class ColorParser implements ParserInterface
{

    public function parseNode(\SimpleXMLElement $element, SkinInterface $skin): Style
    {
        $style = new ColorStyle();
        $style->setName((string) $element['name']);
        $style->setVariable((string) $element['variable']);
        return $style;
    }

}