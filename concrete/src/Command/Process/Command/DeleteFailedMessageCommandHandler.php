<?php

namespace Concrete\Core\Command\Process\Command;

use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;

class DeleteFailedMessageCommandHandler extends AbstractFailedMessageCommandHandler
{

    public function __invoke(DeleteFailedMessageCommand $command)
    {
        $receiver = $this->getReceiverFromCommand($command);
        $message = $receiver->find($command->getMessageId());
        $receiver->reject($message);
        $count = -1;
        if ($receiver instanceof MessageCountAwareInterface) {
            $count = $receiver->getMessageCount();
        }
        return $count;
    }
}