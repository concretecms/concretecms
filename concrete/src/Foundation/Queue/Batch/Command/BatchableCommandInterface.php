<?php

namespace Concrete\Core\Foundation\Queue\Batch\Command;

use Concrete\Core\Foundation\Command\CommandInterface;

interface BatchableCommandInterface extends CommandInterface
{

    public function getBatchHandle();

}