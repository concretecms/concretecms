<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser\Manager;
use Concrete\Core\StyleCustomizer\Style\Parser\ParserInterface;

interface ManagerInterface
{

    public function getParserFromType(string $type): ParserInterface;



}