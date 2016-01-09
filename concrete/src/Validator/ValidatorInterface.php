<?php
namespace Concrete\Core\Validator;

/**
 * Interface ValidatorInterface
 * A generic validator cabable of describing itself and validating mixed values
 *
 * @package Concrete\Core\Validator
 */
interface ValidatorInterface
{

    /**
     * Get the validator requirements in the form of an array keyed by it's respective error code
     *
     * Example:
     *    [ self::E_TOO_SHORT => 'Must be at least 10 characters' ]
     *
     * @return string[]
     */
    public function getRequirementStrings();

    /**
     * Is this mixed value valid
     *
     * @param mixed             $mixed Can be any value
     * @param \ArrayAccess|null $error
     * @return bool
     * @throws \InvalidArgumentException Invalid mixed value type passed.
     */
    public function isValid($mixed, \ArrayAccess $error = null);

}
