<?php

namespace Concrete\Core\Messenger\Batch\Command;

use Concrete\Core\Entity\Messenger\BatchProcess;

class HandleBatchMessageCommand
{

    /**
     * @var object
     */
    protected $message;
    
    /**
     * @var string
     */
    protected $batchProcess;

    public function __construct(string $batchProcess, $message)
    {
        $this->batchProcess = $batchProcess;
        $this->message = $message;
    }

    /**
     * @return object
     */
    public function getMessage(): object
    {
        return $this->message;
    }

    /**
     * @param object $message
     */
    public function setMessage(object $message): void
    {
        $this->message = $message;
    }

    public function getMessageClass(): string
    {
        return get_class($this->message);
    }

    /**
     * @return string
     */
    public function getBatchProcess(): string
    {
        return $this->batchProcess;
    }


}
