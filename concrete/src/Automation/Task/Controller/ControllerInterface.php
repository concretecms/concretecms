<?php

namespace Concrete\Core\Automation\Task\Controller;

use Concrete\Core\Automation\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Automation\Task\Input\InputInterface;
use Concrete\Core\Automation\Task\TaskInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface ControllerInterface
{

    public function getName(): string;

    public function getDescription(): string;

    public function getHelpText(): string;

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface;

}
