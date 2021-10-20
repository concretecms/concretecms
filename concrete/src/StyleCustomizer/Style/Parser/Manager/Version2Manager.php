<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser\Manager;
use Concrete\Core\StyleCustomizer\Style\Parser\ColorParser;
use Concrete\Core\StyleCustomizer\Style\Parser\FontFamilyParser;
use Concrete\Core\StyleCustomizer\Style\Parser\FontStyleParser;
use Concrete\Core\StyleCustomizer\Style\Parser\FontWeightParser;
use Concrete\Core\StyleCustomizer\Style\Parser\ImageParser;
use Concrete\Core\StyleCustomizer\Style\Parser\SizeParser;
use Concrete\Core\StyleCustomizer\Style\Parser\TextDecorationParser;
use Concrete\Core\StyleCustomizer\Style\Parser\TextTransformParser;
use Concrete\Core\StyleCustomizer\Style\Parser\TypeParser;

class Version2Manager extends AbstractManager
{

    public function createColorParser()
    {
        return new ColorParser();
    }

    public function createFontFamilyParser()
    {
        return $this->app->make(FontFamilyParser::class);
    }

    public function createSizeParser()
    {
        return new SizeParser();
    }

    public function createFontStyleParser()
    {
        return new FontStyleParser();
    }

    public function createFontWeightParser()
    {
        return new FontWeightParser();
    }

    public function createTextDecorationParser()
    {
        return new TextDecorationParser();
    }

    public function createTextTransformParser()
    {
        return new TextTransformParser();
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