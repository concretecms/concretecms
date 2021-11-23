<?php

namespace Concrete\Core\StyleCustomizer\Processor;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;

interface ProcessorInterface
{

    public function compileFileToString(string $file, NormalizedVariableCollection $collection): string;


}
