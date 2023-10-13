<?php

namespace Concrete\Core\Install;

abstract class AbstractPrecondition implements PreconditionInterface
{

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'key' => $this->getUniqueIdentifier(),
            'is_optional' => $this->isOptional(),
        ];
    }
}
