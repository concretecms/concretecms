<?php
namespace Concrete\Core\Command\Task\Runner\Context;

use Concrete\Core\Command\Task\Stamp\OutputStamp;

defined('C5_EXECUTE') or die("Access Denied.");

class DashboardContext extends AbstractContext
{

    public function dispatchCommand($command, array $stamps = null): void
    {
        $stamps = [new OutputStamp($this->getOutput())];
        $this->messageBus->dispatch($command, $stamps);
    }


}
