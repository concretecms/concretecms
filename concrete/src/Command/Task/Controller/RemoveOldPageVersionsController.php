<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\ProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Page\Sitemap\Command\GenerateSitemapCommand;

defined('C5_EXECUTE') or die("Access Denied.");

class RemoveOldPageVersionsController extends AbstractController
{

    public function getName(): string
    {
        return t('Remove Old Page Versions');
    }

    public function getDescription(): string
    {
        return t('Removes all except the 10 most recent page versions for each page.');
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        return new ProcessTaskRunner(
            $task,
            new GenerateSitemapCommand(),
            $input,
            t('Generation of sitemap.xml started.')
        );
    }



}
