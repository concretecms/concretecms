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
     * The list of one or more Features this class makes use of.
     *
     * @see \Concrete\Core\Feature\Features The class that contains all the available feature constants
     * @see \Concrete\Block\Gallery\Controller::getRequiredFeatures An example of a core block using a feature
     *
     * @return string[] Feature handles
     */
    public function getRequiredFeatures(): array;

    
}
