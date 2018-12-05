<?php

namespace Concrete\Core\Error\ErrorList\Error;

use Concrete\Core\Error\ErrorList\Field\FieldInterface;

class Error extends AbstractError
{
    /**
     * The error message.
     *
     * @var string
     */
    protected $message;

    /**
     * Class constructor.
     *
     * @param string|mixed $message a string, a scalar, or an object that implements the __toString() method
     * @param \Concrete\Core\Error\ErrorList\Field\FieldInterface|null $field
     */
    public function __construct($message, FieldInterface $field = null)
    {
        $this->setMessage($message);
        if ($field) {
            $this->setField($field);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Error\ErrorList\Error\ErrorInterface::getMessage()
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the error message.
     *
     * @param string|mixed $message a string, a scalar, or an object that implements the __toString() method
     */
    public function setMessage($message)
    {
        $this->message = (string) $message;
    }
}
