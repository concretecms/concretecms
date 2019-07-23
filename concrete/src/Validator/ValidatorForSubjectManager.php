<?php

namespace Concrete\Core\Validator;

use ArrayAccess;

class ValidatorForSubjectManager extends ValidatorManager implements ValidatorManagerForSubjectInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorInterface::isValid()
     */
    public function isValid($mixed, ArrayAccess $error = null)
    {
        return $this->isValidFor($mixed, null, $error);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorForSubjectInterface::isValidFor()
     */
    public function isValidFor($mixed, $subject = null, ArrayAccess $error = null)
    {
        $valid = true;
        foreach ($this->getValidators() as $validator) {
            if ($validator instanceof ValidatorForSubjectInterface) {
                if (!$validator->isValidFor($mixed, $subject, $error)) {
                    $valid = false;
                }
            } elseif (!$validator->isValid($mixed, $error)) {
                $valid = false;
            }
        }

        return $valid;
    }
}
