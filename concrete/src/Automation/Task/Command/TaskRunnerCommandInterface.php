<?php
namespace Concrete\Core\Automation\Task\Command;

use Concrete\Core\Automation\Task\Runner\Response\ResponseInterface;
use Concrete\Core\Foundation\Command\CommandInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface TaskRunnerCommandInterface extends CommandInterface
{

    public function getTaskRunnerResponse(): ResponseInterface;

}
