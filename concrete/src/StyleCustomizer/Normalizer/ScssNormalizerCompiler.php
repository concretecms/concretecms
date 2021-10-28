<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

use ScssPhp\ScssPhp\Compiler as ScssCompiler;
use ScssPhp\ScssPhp\Compiler\Environment;

/**
 * @internal
 */
class ScssNormalizerCompiler extends ScssCompiler
{
    /**
     * @return Environment
     */
    public function getRootEnvironment(): Environment
    {
        return $this->rootEnv;
    }


}
