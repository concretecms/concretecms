<?php
namespace Concrete\Core\Command\Task\Runner\Context;

use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Concrete\Core\Messenger\Stamp\SkipSendersStamp;

defined('C5_EXECUTE') or die("Access Denied.");

class ConsoleContext extends AbstractContext
{

    public function dispatchCommand($command, array $stamps = null): void
    {
        $stamps = [
            new SkipSendersStamp(),
            new OutputStamp($this->getOutput())
        ];
        $this->messageBus->dispatch($command, $stamps);
    }


}
