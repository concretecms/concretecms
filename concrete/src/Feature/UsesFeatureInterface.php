<?php

namespace Concrete\Core\Feature;

interface UsesFeatureInterface
{

    /**
     * @return string[] Feature handles
     */
    public function getRequiredFeatures(): array;

    
}
