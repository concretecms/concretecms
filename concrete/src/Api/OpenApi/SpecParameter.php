<?php

namespace Concrete\Core\Api\OpenApi;

use Concrete\Core\Api\OpenApi\Parameter\ParameterInterface;

abstract class SpecParameter implements ParameterInterface
{

    protected function isRequired()
    {
        return false;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'in' => $this->getIn(),
            'description' => $this->getDescription(),
            'schema' => $this->getSchema(),
            'required' => $this->isRequired(),
        ];
    }


}
