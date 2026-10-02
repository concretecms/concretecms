<?php

namespace Concrete\Core\Api\OpenApi;

use Concrete\Core\Api\OpenApi\Parameter\ParameterInterface;

abstract class SpecParameter implements ParameterInterface
{
    /**
     * Get the name of the parameter.
     *
     * @return string|null
     */
    abstract public function getName();

    /**
     * Get the location of the parameter (path, query, ...).
     *
     * @return string|null
     */
    abstract public function getIn();

    /**
     * Get the description of the parameter.
     *
     * @return string|null
     */
    abstract public function getDescription();

    /**
     * Get the schema of the parameter.
     *
     * @return \Concrete\Core\Api\OpenApi\SpecSchema|null
     */
    abstract public function getSchema();

    /**
     * Is the parameter required?
     *
     * @return bool
     */
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
