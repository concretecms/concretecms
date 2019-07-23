<?php

namespace Concrete\Core\Install;

/**
 * Interface for the checks to be performed before installing concrete5 that need a web environment.
 */
interface WebPreconditionInterface extends PreconditionInterface
{
    /**
     * Get the initial state of the precondition.
     *
     * @return int|null One of the PreconditionResult::STATE_... constants (or NULL if working)
     */
    public function getInitialState();

    /**
     * Get the initial message of the precondition.
     *
     * @return string
     */
    public function getInitialMessage();

    /**
     * Get the HTML used to check the precondition.
     */
    public function getHtml();

    /**
     * Get the answer of ajax calls.
     *
     * @param string $argument
     */
    public function getAjaxAnswer($argument);
}
