<?php

namespace Concrete\Core\StyleCustomizer\Parser\Scss;

use ScssPhp\ScssPhp\Compiler as ScssCompiler;
use ScssPhp\ScssPhp\Compiler\Environment;

class Compiler extends ScssCompiler
{
    /**
     * @return Environment
     */
    public function getRootEnvironment(): Environment
    {
        return $this->rootEnv;
    }


}
