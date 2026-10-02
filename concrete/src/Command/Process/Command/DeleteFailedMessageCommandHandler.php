<?php

namespace Concrete\Core\Command\Process\Command;

use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;

class DeleteFailedMessageCommandHandler extends AbstractFailedMessageCommandHandler
{

    public function __invoke(DeleteFailedMessageCommand $command)
    {
        $receiver = $this->getReceiverFromCommand($command);
        if (!$receiver instanceof ListableReceiverInterface) {
            throw new \RuntimeException(t('The receiver %s does not support finding its messages.', $command->getReceiverName()));
        }
        $message = $receiver->find($command->getMessageId());
        $receiver->reject($message);
        $count = -1;
        if ($receiver instanceof MessageCountAwareInterface) {
            $count = $receiver->getMessageCount();
        }
        return $count;
    }
}