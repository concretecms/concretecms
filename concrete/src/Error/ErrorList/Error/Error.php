<?php
namespace Concrete\Core\Error\ErrorList\Error;

use Concrete\Core\Error\ErrorList\Field\FieldInterface;

class Error extends AbstractError
{

    protected $message;

    /**
     * Error constructor.
     * @param $message
     */
    public function __construct($message, FieldInterface $field = null)
    {
        $this->message = $message;
        if ($field) {
            $this->setField($field);
        }
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }


}
