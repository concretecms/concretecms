<?php
namespace Concrete\Core\Validator;

class ClosureValidator implements ValidatorInterface
{
    /** @var \Closure */
    protected $validator_closure;

    /** @var \Closure */
    protected $requirements_closure;

    /**
     * ClosureValidator constructor.
     *
     * @param \Closure $validator_closure function(ClosureValidator $validator,
     *                                             mixed $passed,
     *                                             \Concrete\Core\Error $error = null): bool
     * @param \Closure $requirements_closure function(ClosureValidator $validator): array
     */
    public function __construct(\Closure $validator_closure, \Closure $requirements_closure)
    {
        $this->validator_closure = $validator_closure;
        $this->requirements_closure = $requirements_closure;
    }

    /**
     * Set the closure that handls validation
     * function(ClosureValidator $validator, mixed $passed, \Concrete\Core\Error $error = null): bool.
     *
     * @param \Closure $validator_closure
     */
    public function setValidatorClosure(\Closure $validator_closure)
    {
        $this->validator_closure = $validator_closure;
    }

    /**
     * Set the closure that returns requirements.
     *
     * @param \Closure $requirements_closure function(ClosureValidator $validator): array
     */
    public function setRequirementsClosure(\Closure $requirements_closure)
    {
        $this->requirements_closure = $requirements_closure;
    }

    /**
     * Get the validator requirements in the form of an array keyed by it's respective error code.
     *
     * Example:
     *    [ self::E_TOO_SHORT => 'Must be at least 10 characters' ]
     *
     * @return string[]
     */
    public function getRequirementStrings()
    {
        $closure = $this->requirements_closure;

        return $closure($this);
    }

    /**
     * Is this mixed value valid.
     *
     * @param mixed             $mixed Can be any value
     * @param \ArrayAccess|null $error
     *
     * @return bool
     *
     * @throws \InvalidArgumentException Invalid mixed value type passed.
     */
    public function isValid($mixed, \ArrayAccess $error = null)
    {
        $closure = $this->validator_closure;

        return $closure($this, $mixed, $error);
    }
}
