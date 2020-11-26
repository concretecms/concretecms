<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Task\Runner\Response\ResponseInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface TaskRunnerInterface
{

    public function getTaskRunnerHandler(): string;

}
