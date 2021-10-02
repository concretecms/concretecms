<?php

namespace Concrete\Core\StyleCustomizer\Processor;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;

class LessProcessor implements ProcessorInterface
{

    public function compileFileToString(string $file, NormalizedVariableCollection $collection): string
    {
        throw new \exception('Not implemented yet.');
    }


}
