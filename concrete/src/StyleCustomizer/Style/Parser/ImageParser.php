<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Concrete\Core\StyleCustomizer\Style\ImageStyle;
use Concrete\Core\StyleCustomizer\Style\Style;

class ImageParser implements ParserInterface
{

    public function parseNode(\SimpleXMLElement $element, SkinInterface $skin): Style
    {
        $style = new ImageStyle();
        $style->setName((string) $element['name']);
        $style->setVariable((string) $element['variable']);
        return $style;
    }

}