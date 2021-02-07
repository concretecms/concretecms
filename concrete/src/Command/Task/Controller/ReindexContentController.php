<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\ProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Page\Sitemap\Command\GenerateSitemapCommand;

defined('C5_EXECUTE') or die("Access Denied.");

class ReindexContentController extends AbstractController
{

    public function getName(): string
    {
        return t('Reindex Content');
    }

    public function getDescription(): string
    {
        return t('Reindex pages, files, users and Express objects.');
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
