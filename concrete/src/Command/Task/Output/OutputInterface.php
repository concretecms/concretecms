<?php
namespace Concrete\Core\Command\Task\Output;

use Concrete\Core\Command\Task\Controller\ControllerInterface;
use Concrete\Core\Command\Task\TaskInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface OutputInterface
{

    public function write($message);


}
