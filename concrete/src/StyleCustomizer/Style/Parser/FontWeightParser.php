<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Concrete\Core\StyleCustomizer\Style\FontWeightStyle;
use Concrete\Core\StyleCustomizer\Style\Style;

class FontWeightParser implements ParserInterface
{

    public function parseNode(\SimpleXMLElement $element, SkinInterface $skin): Style
    {
        $style = new FontWeightStyle();
        $style->setName((string) $element['name']);
        $style->setVariable((string) $element['variable']);
        return $style;
    }

}