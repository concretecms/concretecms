<?php
namespace Concrete\Core\Messenger\Registry;

use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;
use Concrete\Core\Messenger\Transport\Sender\SenderLocator;
/**
 * A bus that extends the basic command bus – but operates only synchronously. Used to execute commands like
 * Rescan File which normally are queued –but when the same command is run from within the consume messages
 * command we want the command to no longer be queueable
 *
 * Class SynchronousCommandBus
 */
class SynchronousCommandBus extends CommandBus
{

    /**
     * Just return an empty senders locator so we're always synchronous
     *
     * @return SendersLocatorInterface
     */
    protected function getSendersLocator(): SendersLocatorInterface
    {
        return new SendersLocator([], new SenderLocator());
    }


}