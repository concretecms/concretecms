<?php

namespace Concrete\Core\Validator;

/**
 * A modifier to the standard validator interface that enables translatable requirements and errors.
 */
interface TranslatableValidatorInterface extends ValidatorInterface
{
    /**
     * Set the requirement string to a mixed value.
     *
     * @param int $code The error code
     * @param string|\Closure $message Either a plain string, or a closure that returns a string
     *
     * @example
     * <code>$message</code> should be like<br />
     * <code>function(TranslatableValidatorInterface $validator, int $code): string</code>
     */
    public function setRequirementString($code, $message);

    /**
     * Set the error string to a string or to a closure.
     *
     * @param int $code The error code
     * @param string|\Closure $message Either a plain string, or a closure that returns a string
     *
     * @example
     * <code>$message</code> should be like<br />
     * <code>function(TranslatableValidatorInterface $validator, int $code, mixed $passed): string</code><br />
     * where <code>$passed</code> is whatever was passed to <code>ValidatorInterface::isValid</code>
     */
    public function setErrorString($code, $message);
}
