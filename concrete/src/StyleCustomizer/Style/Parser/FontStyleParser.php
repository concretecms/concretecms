<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Style\FontStyleStyle;
use Concrete\Core\StyleCustomizer\Style\Style;

class FontStyleParser implements ParserInterface
{

    public function parseNode(\SimpleXMLElement $element, PresetInterface $preset): Style
    {
        $style = new FontStyleStyle();
        $style->setName((string) $element['name']);
        $style->setVariable((string) $element['variable']);
        return $style;
    }

}