<?php
namespace Concrete\Core\Command\Batch;

use Concrete\Core\Command\Batch\Command\HandleBatchMessageCommand;
use Concrete\Core\Command\Batch\Stamp\BatchStamp;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Concrete\Core\Entity\Command\Batch as BatchEntity;
use Concrete\Core\Messenger\Transport\TransportManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

class Listener
{

    public function preRemove(BatchEntity $batch, LifecycleEventArgs $args)
    {
        $receivers = app(TransportManager::class)->getReceivers()->getAll();
        foreach($receivers as $receiver) {
            if ($receiver instanceof ListableReceiverInterface) {
                foreach($receiver->all() as $envelope) {
                    $batchStamp = $envelope->last(BatchStamp::class);
                    if ($batchStamp && $batchStamp->getBatchId() == $batch->getId()) {
                        // This was a message on that batch, so we're going to reject and remove the message.
                        $receiver->reject($envelope);
                    }
                }
            }
        }
    }



}
