<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Style\Style;

interface ParserInterface
{

    public function parseNode(\SimpleXMLElement $element) :Style;

}