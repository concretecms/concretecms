<?php
namespace Concrete\Core\Error\ErrorBag\Error;

use Concrete\Core\Error\ErrorBag\Field\FieldInterface;

class ExceptionError extends AbstractError
{

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * ExceptionError constructor.
     * @param $exception
     */
    public function __construct(\Exception $exception, FieldInterface $field = null)
    {
        $this->exception = $exception;
        if ($field) {
            $this->setField($field);
        }
    }

    /**
     * @return mixed
     */
    public function getException()
    {
        return $this->exception;
    }

    public function getMessage()
    {
        return $this->exception->getMessage();
    }

}
