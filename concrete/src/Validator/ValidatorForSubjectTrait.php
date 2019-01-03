<?php

namespace Concrete\Core\Validator;

use ArrayAccess;

/**
 * A trait that provides an implementation of ValidatorInterface::isValid
 */
trait ValidatorForSubjectTrait
{

    /**
     * @see \Concrete\Core\Validator\ValidatorInterface::isValid()
     */
    public function isValid($mixed, ArrayAccess $error = null)
    {
        return $this->isValidFor($mixed, null, $error);
    }
}
