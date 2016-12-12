<?php
namespace Concrete\Core\Validator;

/**
 * Interface TranslatableValidatorInterface
 * A modifier to the standard validator interface that enables translatable requirements and errors.
 *
 * \@package Concrete\Core\Validator
 */
interface TranslatableValidatorInterface extends ValidatorInterface
{
    /**
     * Set the requirement string to a mixed value
     * Closure format:
     *    function(TranslatableValidatorInterface $validator, int $code): string.
     *
     * @param int             $code    The error code
     * @param string|\Closure $message Either a plain string, or a closure that returns a string
     */
    public function setRequirementString($code, $message);

    /**
     * Set the error string to a string or to a closure
     * Closure format:
     *    function(TranslatableValidatorInterface $validator, int $code, mixed $passed): string.
     *
     * where `$passed` is whatever was passed to `ValidatorInterface::isValid`
     *
     * @param int             $code    The error code
     * @param string|\Closure $message Either a plain string, or a closure that returns a string
     */
    public function setErrorString($code, $message);
}
