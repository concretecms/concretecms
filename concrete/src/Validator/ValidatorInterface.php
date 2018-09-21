<?php

namespace Concrete\Core\Validator;

use ArrayAccess;

/**
 * A generic validator cabable of describing itself and validating mixed values.
 */
interface ValidatorInterface
{
    /**
     * Get the validator requirements in the form of an array keyed by it's respective error code.
     *
     * @return string[]
     *
     * @example
     * <code>[self::E_TOO_SHORT => 'Must be at least 10 characters']</code>
     */
    public function getRequirementStrings();

    /**
     * Is this mixed value valid.
     *
     * @param mixed $mixed Can be any value
     * @param \ArrayAccess|null $error
     *
     * @throws \InvalidArgumentException invalid mixed value type passed
     *
     * @return bool
     */
    public function isValid($mixed, ArrayAccess $error = null);
}
