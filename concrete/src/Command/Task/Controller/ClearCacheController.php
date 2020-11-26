<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\CommandTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Cache\Command\ClearCacheCommand;

defined('C5_EXECUTE') or die("Access Denied.");

class ClearCacheController extends AbstractController
{

    public function getName(): string
    {
        return t('Clear Cache');
    }

    public function getDescription(): string
    {
        return t('Clears all caches.');
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        return new CommandTaskRunner($task, new ClearCacheCommand(), t('Cache cleared successfully.'));
    }


}
