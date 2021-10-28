<?php

namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Entity\Command\Process;

defined('C5_EXECUTE') or die("Access Denied.");

interface ProcessTaskRunnerInterface extends TaskRunnerInterface
{

    public function getProcess(): Process;


}
