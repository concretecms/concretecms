<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Style\Style;
use Concrete\Core\StyleCustomizer\Style\StyleInterface;

interface ParserInterface
{

    public function parseNode(\SimpleXMLElement $element, PresetInterface $preset) :StyleInterface;

}