<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Style\FontWeightStyle;
use Concrete\Core\StyleCustomizer\Style\Style;

class FontWeightParser implements ParserInterface
{

    public function parseNode(\SimpleXMLElement $element, PresetInterface $preset): Style
    {
        $style = new FontWeightStyle();
        $style->setName((string) $element['name']);
        $style->setVariable((string) $element['variable']);
        return $style;
    }

}