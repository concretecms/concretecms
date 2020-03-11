<?php

namespace Concrete\Core\Feature;

/**
 * Denotes that a particular block controller uses features, and allows the block controller to specify
 * which features it requires to function. This allows blocks like the calendar, for example, to specify that
 * they require the Calendar feature to function, which lets them use the feature fallback if the current
 * theme doesn't support those features.
 */
interface UsesFeatureInterface
{

    /**
     * @return string[] Feature handles
     */
    public function getRequiredFeatures(): array;

    
}
