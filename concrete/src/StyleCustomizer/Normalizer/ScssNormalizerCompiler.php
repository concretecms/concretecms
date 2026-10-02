<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

use ScssPhp\ScssPhp\Compiler as ScssCompiler;
use ScssPhp\ScssPhp\Compiler\Environment;

/**
 * @internal
 * @phpstan-ignore class.extendsFinalByPhpDoc (the parent class is marked as final only in its PHPDoc: extending it is a known and accepted risk)
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
