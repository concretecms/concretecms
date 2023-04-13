<?php

namespace Concrete\Core\Install\Command;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Install\ExecutedPrecondition;

class ValidateEnvironmentCommandResponse implements \JsonSerializable
{

    /**
     * @var ErrorList
     */
    protected $error;

    /**
     * @var ExecutedPrecondition[]
     */
    protected $preconditions = [];

    /**
     * @return ErrorList
     */
    public function getError(): ErrorList
    {
        return $this->error;
    }

    /**
     * @param ErrorList $error
     */
    public function setError(ErrorList $error): void
    {
        $this->error = $error;
    }

    /**
     * @return ExecutedPrecondition[]
     */
    public function getPreconditions(): array
    {
        return $this->preconditions;
    }

    /**
     * @param ExecutedPrecondition[] $preconditions
     */
    public function setPreconditions(array $preconditions): void
    {
        $this->preconditions = $preconditions;
    }

    public function jsonSerialize()
    {
        return [
            'error' => $this->error,
            'preconditions' => $this->preconditions,
        ];
    }


}
