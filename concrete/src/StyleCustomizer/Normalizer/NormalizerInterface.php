<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

interface NormalizerInterface
{

    public function createVariableCollectionFromFile(string $variablesFilePath) :NormalizedVariableCollection;

}
