<?php
namespace Concrete\Core\Automation\Task;

use Concrete\Core\Automation\Task\Controller\ControllerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface TaskInterface
{

    public function getController(): ControllerInterface;

}
