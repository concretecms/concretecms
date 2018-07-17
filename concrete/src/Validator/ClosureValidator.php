<?php

namespace Concrete\Core\Validator;

use ArrayAccess;
use Closure;

/**
 * A generic validator cabable of describing itself and validating mixed values using closures.
 */
class ClosureValidator implements ValidatorInterface
{
    /**
     * The closure that handles validation.
     *
     * @var \Closure
     */
    protected $validator_closure;

    /**
     * The closure that returns requirements.
     *
     *@var \Closure
     */
    protected $requirements_closure;

    /**
     * ClosureValidator constructor.
     *
     * @param \Closure $validator_closure function(ClosureValidator $validator, mixed $passed, \Concrete\Core\Error $error = null): bool
     * @param \Closure $requirements_closure function(ClosureValidator $validator): array
     *
     * @example
     */
    public function __construct(Closure $validator_closure, Closure $requirements_closure)
    {
        $this->validator_closure = $validator_closure;
        $this->requirements_closure = $requirements_closure;
    }

    /**
     * Set the closure that handles validation.
     *
     * @param \Closure $validator_closure
     *
     * @example function(ClosureValidator $validator, mixed $passed, \Concrete\Core\Error $error = null): bool
     */
    public function setValidatorClosure(Closure $validator_closure)
    {
        $this->validator_closure = $validator_closure;
    }

    /**
     * Set the closure that returns requirements.
     *
     * @param \Closure $requirements_closure
     *
     * @example function(ClosureValidator $validator): array
     */
    public function setRequirementsClosure(Closure $requirements_closure)
    {
        $this->requirements_closure = $requirements_closure;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorInterface::getRequirementStrings()
     */
    public function getRequirementStrings()
    {
        $closure = $this->requirements_closure;

        return $closure($this);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorInterface::isValid()
     */
    public function isValid($mixed, ArrayAccess $error = null)
    {
        $closure = $this->validator_closure;

        return $closure($this, $mixed, $error);
    }
}
