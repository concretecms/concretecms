<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Task\Output\OutputInterface;
use Concrete\Core\Command\Task\Runner\Response\ResponseInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface HandlerInterface
{

    public function start(TaskRunnerInterface $runner, OutputInterface $output);

    public function run(TaskRunnerInterface $runner, OutputInterface $output);

    public function complete(TaskRunnerInterface $runner, OutputInterface $output): ResponseInterface;


}
