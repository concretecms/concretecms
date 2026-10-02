<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Style\ColorStyle;
use Concrete\Core\StyleCustomizer\Style\Style;
use Concrete\Core\StyleCustomizer\Style\StyleInterface;

abstract class AbstractParser implements ParserInterface
{

    abstract public function createStyleObject(): StyleInterface;

    public function parseNode(\SimpleXMLElement $element, PresetInterface $preset): StyleInterface
    {
        $style = $this->createStyleObject();
        $this->loadFromXml($style, $element);
        return $style;
    }

    protected function loadFromXml(StyleInterface $style, \SimpleXMLElement $element)
    {
        if (!method_exists($style, 'setName') || !method_exists($style, 'setVariable')) {
            throw new \InvalidArgumentException(t('The style must have the %s and %s methods.', 'setName()', 'setVariable()'));
        }
        $style->setName((string) $element['name']);
        $style->setVariable((string) $element['variable']);
        return $style;
    }


}