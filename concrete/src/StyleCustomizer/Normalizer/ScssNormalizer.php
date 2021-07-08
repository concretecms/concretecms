<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

use Illuminate\Filesystem\Filesystem;

class ScssNormalizer implements NormalizerInterface
{

    /**
     * @var ScssNormalizerCompiler
     */
    protected $compiler;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * ScssNormalizer constructor.
     * @param ScssNormalizerCompiler $compiler
     * @param Filesystem $fileSystem
     */
    public function __construct(ScssNormalizerCompiler $compiler, Filesystem $fileSystem)
    {
        $this->compiler = $compiler;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param string $variableName
     * @return string
     */
    private function fixVariable(string $variableName)
    {
        // Why is this necessary? The way the Scss Parser works it stores the variable names as the keys of the PHP
        // array. Unfortunately that means no "-". Since SCSS uses "-" pretty much exclusively let's just force it
        // and turn all _ back into -
        return str_replace('_', '-', $variableName);
    }

    public function createVariableCollectionFromFile(string $variablesFilePath): NormalizedVariableCollection
    {
        $contents = $this->fileSystem->get($variablesFilePath);
        $compiler = new ScssNormalizerCompiler();
        $compiler->compileString($contents);
        $environment = $compiler->getRootEnvironment();

        $collection = new NormalizedVariableCollection();

        foreach($environment->store as $variable => $value) {
            if ($value[0] == 'keyword') {
                $collection->add(new Variable($this->fixVariable($variable), $value[1]));
            }
            if ($value[0] == 'string') {
                $collection->add(new Variable($this->fixVariable($variable), $value[2][0])); // not sure why it's this way
            }
        }

        return $collection;

    }


}
