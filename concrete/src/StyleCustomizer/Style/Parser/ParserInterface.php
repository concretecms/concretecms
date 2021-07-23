<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Concrete\Core\StyleCustomizer\Style\Style;

interface ParserInterface
{

    public function parseNode(\SimpleXMLElement $element, SkinInterface $skin) :Style;

}