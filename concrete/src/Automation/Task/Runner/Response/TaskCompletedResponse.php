<?php

namespace Concrete\Core\Automation\Task\Runner\Response;

class TaskCompletedResponse implements ResponseInterface
{

    /**
     * @var string
     */
    protected $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }


}
