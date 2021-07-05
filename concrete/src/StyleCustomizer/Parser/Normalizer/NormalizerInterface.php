<?php

namespace Concrete\Core\StyleCustomizer\Parser\Normalizer;

interface NormalizerInterface
{

    public function createVariableCollectionFromFile(string $variablesFilePath) :NormalizedVariableCollection;

}
