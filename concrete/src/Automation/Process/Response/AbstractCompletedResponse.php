<?php

namespace Concrete\Core\Automation\Process\Response;

abstract class AbstractCompletedResponse implements CompletedResponseInterface
{

    /**
     * @var
     */
    protected $message;

    public function __construct($message = null)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }


}
