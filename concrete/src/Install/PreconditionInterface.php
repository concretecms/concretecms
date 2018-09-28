<?php

namespace Concrete\Core\Install;

/**
 * Interface for the checks to be performed before installing concrete5.
 */
interface PreconditionInterface
{
    /**
     * Get the precondition name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the precondition handle.
     *
     * @return string
     */
    public function getUniqueIdentifier();

    /**
     * Get the precondition result.
     *
     * @return PreconditionResult
     */
    public function performCheck();

    /**
     * Is this an optional precondition?
     *
     * @return bool
     */
    public function isOptional();
}
