<?php
namespace Concrete\Core\Command\Task\Runner\Context;

use Concrete\Core\Command\Task\Stamp\OutputStamp;

defined('C5_EXECUTE') or die("Access Denied.");

class DashboardContext extends AbstractContext
{

    public function dispatchCommand($command, array $stamps = null): void
    {
        $newStamps = [new OutputStamp($this->getOutput())];
        if (!is_null($stamps)) {
            $stamps = array_merge($newStamps, $stamps);
        } else {
            $stamps = $newStamps;
        }
        $this->messageBus->dispatch($command, $stamps);
    }


}
