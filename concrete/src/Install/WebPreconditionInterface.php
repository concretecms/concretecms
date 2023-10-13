<?php

namespace Concrete\Core\Install;

/**
 * Interface for the checks to be performed before installing concrete5 that need a web environment.
 */
interface WebPreconditionInterface extends PreconditionInterface
{

    /**
     * Get the answer of ajax calls.
     *
     * @param string $argument
     */
    public function getAjaxAnswer($argument);
}
