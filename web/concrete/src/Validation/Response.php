<?php

namespace Concrete\Core\Validation;

use Concrete\Core\Error\Error;

class Response implements ResponseInterface
{

    protected $valid;
    protected $error;

    public function __construct()
    {
        $this->setIsValid(true);
        $this->setErrorObject(new Error());
    }

    /**
     * @return mixed
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @param mixed $valid
     */
    public function setIsValid($valid)
    {
        $this->valid = $valid;
    }

    /**
     * @return mixed
     */
    public function getErrorObject()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setErrorObject($error)
    {
        $this->error = $error;
    }



}
