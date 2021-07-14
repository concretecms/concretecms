<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Manager;

class ParserManager extends Manager
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function createColorDriver()
    {
        return new ColorParser();
    }

    public function createFontFamilyDriver()
    {
        return new FontFamilyParser();
    }

    public function createSizeDriver()
    {
        return new SizeParser();
    }


}