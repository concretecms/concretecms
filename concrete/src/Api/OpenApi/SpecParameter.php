<?php

namespace Concrete\Core\Api\OpenApi;

use Concrete\Core\Api\OpenApi\Parameter\ParameterInterface;

class SpecParameter implements ParameterInterface
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $in;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var SpecSchema
     */
    protected $schema;

    /**
     * SpecParameter constructor.
     * @param string $name
     * @param string $in
     * @param string $description
     */
    public function __construct(string $name = null, string $in = null, string $description = null)
    {
        $this->name = $name;
        $this->in = $in;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIn(): ?string
    {
        return $this->in;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return SpecSchema
     */
    public function getSchema(): ?SpecSchema
    {
        return $this->schema;
    }


    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'in' => $this->getIn(),
            'description' => $this->getDescription(),
            'schema' => $this->getSchema(),
        ];
    }


}
