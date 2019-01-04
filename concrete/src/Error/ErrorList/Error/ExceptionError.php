<?php

namespace Concrete\Core\Error\ErrorList\Error;

use Concrete\Core\Error\ErrorList\Field\FieldInterface;
use Exception;

class ExceptionError extends AbstractError
{
    /**
     * The associated Exception.
     *
     * @var \Exception
     */
    protected $exception;

    /**
     * Class constructor.
     *
     * @param \Exception $exception
     * @param \Concrete\Core\Error\ErrorList\Field\FieldInterface|null $field
     */
    public function __construct(Exception $exception, FieldInterface $field = null)
    {
        $this->exception = $exception;
        if ($field) {
            $this->setField($field);
        }
    }

    /**
     * Get the associated Exception.
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Error\ErrorList\Error\ErrorInterface::getMessage()
     */
    public function getMessage()
    {
        return $this->getException()->getMessage();
    }
}
