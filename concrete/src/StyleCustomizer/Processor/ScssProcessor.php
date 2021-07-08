<?php

namespace Concrete\Core\StyleCustomizer\Processor;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Illuminate\Filesystem\Filesystem;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\ValueConverter;

class ScssProcessor implements ProcessorInterface
{

    /**
     * @var Compiler
     */
    protected $compiler;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * ScssProcessor constructor.
     * @param Compiler $compiler
     */
    public function __construct(Filesystem $filesystem, Compiler $compiler)
    {
        $this->filesystem = $filesystem;
        $this->compiler = $compiler;
    }

    public function compileFileToString(string $file, NormalizedVariableCollection $collection): string
    {
        $scss = $this->filesystem->get($file);
        $this->compiler->addImportPath(dirname($file)); // so that relative imports work
        $variables = [];
        foreach ($collection->getValues() as $variable) {
            $variables[$variable->getName()] = ValueConverter::parseValue($variable->getValue());
        }
        $this->compiler->replaceVariables($variables);
        $css = $this->compiler->compileString($scss)->getCss();
        return $css;
    }

    
}
