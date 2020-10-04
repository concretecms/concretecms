<?php
namespace Concrete\Core\Automation\Task\Runner;

use Concrete\Core\Automation\Task\Runner\Response\ResponseInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface TaskRunnerInterface
{

    public function getTaskRunnerResponse(): ResponseInterface;

}
