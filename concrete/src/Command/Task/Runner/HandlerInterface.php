<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Task\Runner\Context\ContextInterface;
use Concrete\Core\Command\Task\Runner\Response\ResponseInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface HandlerInterface
{

    public function boot(TaskRunnerInterface $runner);

    public function start(TaskRunnerInterface $runner, ContextInterface $context);

    public function run(TaskRunnerInterface $runner, ContextInterface $context);

    public function complete(TaskRunnerInterface $runner, ContextInterface $context): ResponseInterface;


}
