<?php

namespace Concrete\Core\Validator;

use ArrayAccess;

/**
 * A generic validator cabable of describing itself and validating mixed values, with the possibility to specify the subject for whom a value is valid.
 */
interface ValidatorForSubjectInterface extends ValidatorInterface
{
    /**
     * Is this mixed value valid for the specified (optional) subject?
     *
     * @param mixed $mixed Can be any value
     * @param mixed $subject The subject the value should be valid for
     * @param \ArrayAccess|null $error
     *
     * @throws \InvalidArgumentException throws a InvalidArgumentException when $mixed or $subject are not valid
     *
     * @return bool
     */
    public function isValidFor($mixed, $subject = null, ArrayAccess $error = null);
}
