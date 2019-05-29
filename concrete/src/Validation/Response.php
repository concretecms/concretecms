<?php

namespace Concrete\Core\Validation;

use Concrete\Core\Error\ErrorList\ErrorList;

class Response implements ResponseInterface
{

    /**
     * @var bool
     */
    protected $valid;

    /**
     * @var ErrorList
     */
    protected $error;

    /**
     * Response constructor.
     */
    public function __construct()
    {
        $this->setIsValid(true);
        $this->setErrorObject(new ErrorList());
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     */
    public function setIsValid($valid)
    {
        $this->valid = $valid;
    }

    /**
     * @return ErrorList
     */
    public function getErrorObject()
    {
        return $this->error;
    }

    /**
     * @param ErrorList $error
     */
    public function setErrorObject(ErrorList $error)
    {
        if ($error->has()) {
            $this->setIsValid(false);
        }
        $this->error = $error;
    }



}
