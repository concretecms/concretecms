<?php

namespace Concrete\Core\Api\OpenApi\Parameter;

use Concrete\Core\Api\OpenApi\SpecParameter;

class Parameter extends SpecParameter
{

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $in;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var \Concrete\Core\Api\OpenApi\SpecSchema|null
     */
    protected $specSchema;

    /**
     * @var bool
     */
    protected $isRequired = false;

    /**
     * @param string|null $name
     * @param string|null $in
     * @param string|null $description
     * @param \Concrete\Core\Api\OpenApi\SpecSchema|null $specSchema
     * @param bool $required
     */
    public function __construct($name, $in, $description, $specSchema, $required = false)
    {
        $this->name = $name;
        $this->in = $in;
        $this->description = $description;
        $this->specSchema = $specSchema;
        $this->isRequired = $required;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\OpenApi\SpecParameter::getName()
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\OpenApi\SpecParameter::getIn()
     */
    public function getIn(): ?string
    {
        return $this->in;
    }

    /**
     * @param string|null $in
     */
    public function setIn($in): void
    {
        $this->in = $in;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\OpenApi\SpecParameter::getDescription()
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\OpenApi\SpecParameter::getSchema()
     */
    public function getSchema()
    {
        return $this->specSchema;
    }

    /**
     * @param \Concrete\Core\Api\OpenApi\SpecSchema|null $specSchema
     */
    public function setSchema($specSchema): void
    {
        $this->specSchema = $specSchema;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\OpenApi\SpecParameter::isRequired()
     */
    public function isRequired()
    {
        return $this->isRequired;
    }
}
