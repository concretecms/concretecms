<?php

namespace Concrete\Core\Api\OpenApi\Parameter;

use Concrete\Core\Api\OpenApi\SpecParameter;

class Parameter extends SpecParameter
{

    protected $name;

    protected $in;

    protected $description;

    protected $specSchema;

    protected $isRequired = false;
    /**
     * @param $name
     * @param $in
     * @param $description
     * @param $specSchema
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
     * @return mixed
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getIn(): ?string
    {
        return $this->in;
    }

    /**
     * @param mixed $in
     */
    public function setIn($in): void
    {
        $this->in = $in;
    }

    /**
     * @return mixed
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getSchema()
    {
        return $this->specSchema;
    }

    /**
     * @param mixed $specSchema
     */
    public function setSchema($specSchema): void
    {
        $this->specSchema = $specSchema;
    }

    /**
     * @return bool|mixed
     */
    public function isRequired()
    {
        return $this->isRequired;
    }



}
