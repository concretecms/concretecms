<?php

namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Entity\Command\Process;

defined('C5_EXECUTE') or die("Access Denied.");

interface ProcessTaskRunnerInterface
{

    public function getProcess(): Process;


}
