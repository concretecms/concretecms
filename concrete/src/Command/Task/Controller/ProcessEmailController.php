<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\ProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Mail\Command\ProcessEmailCommand;

defined('C5_EXECUTE') or die("Access Denied.");

class ProcessEmailController extends AbstractController
{

    public function getName(): string
    {
        return t('Process Email');
    }

    public function getDescription(): string
    {
        return t('Polls an email account and grabs private messages/postings that are sent there.');
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        return new ProcessTaskRunner(
            $task,
            new ProcessEmailCommand(),
            $input,
            t('Scanning email accounts...')
        );
    }



}
