<?php
namespace Concrete\Core\Automation\Task\Controller;

use Concrete\Core\Automation\Task\Command\ExecuteSimpleTaskCommand;
use Concrete\Core\Automation\Task\Command\TaskRunnerCommandInterface;
use Concrete\Core\Automation\Task\Input\InputInterface;
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

    public function getTaskRunnerCommand(InputInterface $input): TaskRunnerCommandInterface
    {
        return new ExecuteSimpleTaskCommand(new ClearCacheCommand(), t('Cache cleared successfully.'));
    }



}
