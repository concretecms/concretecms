<?php

namespace Concrete\Core\Conversation\Command;

use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Foundation\Command\Command;

defined('C5_EXECUTE') or die('Access Denied.');

abstract class AbstractConversationMessageCommand extends Command
{

    /**
     * @var Message
     */
    protected $message;

    /**
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }



}