<?php
namespace Concrete\Core\Automation\Task\Controller;

use Concrete\Core\Automation\Task\Command\TaskRunnerCommandInterface;
use Concrete\Core\Automation\Task\Input\InputInterface;

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

    public function getTaskRunnerCommand(InputInterface $input): TaskRunnerCommandInterface
    {

    }
}
