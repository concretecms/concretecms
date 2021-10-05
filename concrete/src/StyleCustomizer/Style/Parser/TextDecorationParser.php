<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Style\Style;
use Concrete\Core\StyleCustomizer\Style\TextDecorationStyle;

class TextDecorationParser implements ParserInterface
{

    public function parseNode(\SimpleXMLElement $element, PresetInterface $preset): Style
    {
        $style = new TextDecorationStyle();
        $style->setName((string) $element['name']);
        $style->setVariable((string) $element['variable']);
        return $style;
    }

}