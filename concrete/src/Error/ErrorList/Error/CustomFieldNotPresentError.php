<?php

namespace Concrete\Core\Error\ErrorList\Error;

use Concrete\Core\Error\ErrorList\Field\FieldInterface;

class CustomFieldNotPresentError extends FieldNotPresentError
{

    /**
     * @var string
     */
    protected $message;

    /**
     * Class constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
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
}
