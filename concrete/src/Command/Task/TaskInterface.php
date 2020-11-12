<?php
namespace Concrete\Core\Command\Task;

use Concrete\Core\Command\Task\Controller\ControllerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface TaskInterface
{

    public function getController(): ControllerInterface;

}
