<?php

namespace Concrete\Core\Validator;

/**
 * A generic validator manager interface that enables validating against many validators at once.
 */
interface ValidatorManagerInterface extends ValidatorInterface
{
    /**
     * Get a list of all validators keyed by their handles.
     *
     * @return \Concrete\Core\Validator\ValidatorInterface[]
     */
    public function getValidators();

    /**
     * Does a validator with this handle exist?
     *
     * @param string $handle
     *
     * @return bool
     */
    public function hasValidator($handle);

    /**
     * Add a validator to the stack.
     * Validators are unique by handle, so adding a validator with the same handle as a validator in the stack
     * replaces the old validator with the new one.
     *
     * @param string $handle
     * @param \Concrete\Core\Validator\ValidatorInterface $validator
     */
    public function setValidator($handle, ValidatorInterface $validator = null);
}
