<?php
namespace Concrete\Core\Automation\Task\Controller;

use Concrete\Core\Automation\Task\Input\InputInterface;
use Concrete\Core\Automation\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Automation\Task\TaskInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class RescanFilesController extends AbstractController
{

    public function getName(): string
    {
        return t('Rescan Files');
    }

    public function getDescription(): string
    {
        return t('Recomputes all attributes, clears and regenerates all thumbnails for a file.');
    }

    public function getTaskRunnerCommand(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {

    }
}
