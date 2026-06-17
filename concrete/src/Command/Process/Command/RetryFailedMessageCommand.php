<?php

namespace Concrete\Core\Command\Process\Command;

use Concrete\Core\Foundation\Command\Command;

class RetryFailedMessageCommand extends Command
{


    protected $messageId;

    protected $receiverName;

    public function __construct($messageId, ?string $receiverName = null)
    {
        $this->messageId = $messageId;
        $this->receiverName = $receiverName;
    }

    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @return mixed
     */
    public function getReceiverName()
    {
        return $this->receiverName;
    }




}