<?php

namespace Concrete\Core\Automation\Task\Controller;

use Concrete\Core\Automation\Process\Response\ResponseInterface;
use Concrete\Core\Automation\Task\Input\InputInterface;
use Concrete\Core\Foundation\Command\CommandInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface ControllerInterface
{

    public function getName(): string;

    public function getDescription(): string;

    public function getHelpText(): string;

    public function getCommand(InputInterface $input): CommandInterface;

}
