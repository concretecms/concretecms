<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser\Manager;
use Concrete\Core\StyleCustomizer\Style\Parser\Legacy\ColorParser;
use Concrete\Core\StyleCustomizer\Style\Parser\Legacy\ImageParser;
use Concrete\Core\StyleCustomizer\Style\Parser\Legacy\SizeParser;
use Concrete\Core\StyleCustomizer\Style\Parser\Legacy\TypeParser;

class Version1Manager extends AbstractManager
{

    public function createColorParser()
    {
        return new ColorParser();
    }

    public function createSizeParser()
    {
        return new SizeParser();
    }

    public function createImageParser()
    {
        return new ImageParser();
    }

    public function createTypeParser()
    {
        return $this->app->make(TypeParser::class);
    }


}