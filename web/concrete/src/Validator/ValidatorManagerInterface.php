<?php
namespace Concrete\Core\Validator;

/**
 * Interface ValidatorManagerInterface
 * A generic validator manager interface that enables validating against many validators at once
 *
 * @package Concrete\Core\Validator
 */
interface ValidatorManagerInterface extends ValidatorInterface
{

    /**
     * Get a list of all validators
     *
     * @return ValidatorInterface[] Array of validators keyed by their handles
     */
    public function getValidators();

    /**
     * Does a validator with this handle exist
     *
     * @param string $handle
     * @return bool
     */
    public function hasValidator($handle);

    /**
     * Add a validator to the stack.
     * Validators are unique by handle, so adding a validator with the same handle as a validator in the stack
     * replaces the old validator with the new one.
     *
     * @param string                                      $handle
     * @param \Concrete\Core\Validator\ValidatorInterface $validator
     * @return void
     */
    public function setValidator($handle, ValidatorInterface $validator=null);

    /**
     * Is this mixed value valid based on the added validators
     *
     * @param mixed             $mixed Can be any value
     * @param \ArrayAccess|null $error The error object that will contain the error strings
     * @return bool
     * @throws \InvalidArgumentException Invalid mixed value type passed.
     * @todo Move out of this comment so that we can properly hint
     *
    public function isValid($mixed, \ArrayAccess $error = null);
     */

}
