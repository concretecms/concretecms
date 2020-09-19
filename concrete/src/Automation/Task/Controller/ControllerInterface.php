<?php

namespace Concrete\Core\Automation\Task\Controller;

use Concrete\Core\Automation\Task\Command\TaskRunnerCommandInterface;
use Concrete\Core\Automation\Task\Input\InputInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface ControllerInterface
{

    public function getName(): string;

    public function getDescription(): string;

    public function getHelpText(): string;

    public function getTaskRunnerCommand(InputInterface $input): TaskRunnerCommandInterface;

}
