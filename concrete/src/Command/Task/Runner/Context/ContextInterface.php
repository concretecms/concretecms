<?php
namespace Concrete\Core\Command\Task\Runner\Context;

use Concrete\Core\Command\Task\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface ContextInterface
{

    public function getOutput(): OutputInterface;

    public function dispatchCommand($command, array $stamps = null): void;

}
