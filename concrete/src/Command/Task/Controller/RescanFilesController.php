<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;

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

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {

    }
}
