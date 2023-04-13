<?php

namespace Concrete\Core\Install;

class ExecutedPreconditionList implements \JsonSerializable
{

    /**
     * @var ExecutedPrecondition[]
     */
    protected $results = [];

    public function addPrecondition(ExecutedPrecondition $precondition)
    {
        $this->results[] = $precondition;
    }

    /**
     * @return ExecutedPrecondition[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->results;
    }

}
