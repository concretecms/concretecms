<?php

namespace Concrete\Core\StyleCustomizer\Processor;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;

class LessProcessor implements ProcessorInterface
{

    /**
     * @var \Less_Parser
     */
    protected $parser;

    public function __construct(\Less_Parser $parser)
    {
        $this->parser = $parser;
    }


    public function compileFileToString(string $file, NormalizedVariableCollection $collection): string
    {
        $parser = $this->parser->parseFile($file);
        $variables = [];
        foreach ($collection->getValues() as $variable) {
            $variables[$variable->getName()] = (string) $variable->getValue();
        }
        $parser->ModifyVars($variables);
        $css = $parser->getCss();
        return $css;
    }


}
