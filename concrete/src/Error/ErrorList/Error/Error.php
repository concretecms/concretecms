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
     * @param string $message
     * @param \Concrete\Core\Error\ErrorList\Field\FieldInterface|null $field
     */
    public function __construct($message, FieldInterface $field = null)
    {
        $this->message = $message;
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
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}
