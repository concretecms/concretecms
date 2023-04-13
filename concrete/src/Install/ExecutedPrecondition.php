<?php

namespace Concrete\Core\Install;

/**
 * Joins a precondition to its result.
 */
class ExecutedPrecondition implements \JsonSerializable
{

    /**
     * @var PreconditionResult|null
     */
    protected $result;

    /**
     * @var PreconditionInterface
     */
    protected $precondition;

    public function __construct(?PreconditionResult $result, PreconditionInterface $precondition)
    {
        $this->result = $result;
        $this->precondition = $precondition;
    }

    /**
     * @return PreconditionResult|null
     */
    public function getResult(): ?PreconditionResult
    {
        return $this->result;
    }

    /**
     * @param PreconditionResult|null $result
     */
    public function setResult(?PreconditionResult $result): void
    {
        $this->result = $result;
    }

    /**
     * @return PreconditionInterface
     */
    public function getPrecondition(): PreconditionInterface
    {
        return $this->precondition;
    }

    /**
     * @param PreconditionInterface $precondition
     */
    public function setPrecondition(PreconditionInterface $precondition): void
    {
        $this->precondition = $precondition;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'result' => $this->getResult(),
            'precondition' => $this->getPrecondition(),
        ];
    }


}
