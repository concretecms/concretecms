<?php

namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\TaskInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface ControllerInterface
{

    public function getName(): string;

    public function getDescription(): string;

    public function getHelpText(): string;

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface;

}
