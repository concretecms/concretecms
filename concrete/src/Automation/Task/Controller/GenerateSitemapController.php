<?php
namespace Concrete\Core\Automation\Task\Controller;

use Concrete\Core\Automation\Task\Input\InputInterface;
use Concrete\Core\Automation\Task\Runner\ProcessTaskRunner;
use Concrete\Core\Automation\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Automation\Task\TaskInterface;
use Concrete\Core\Page\Sitemap\Command\GenerateSitemapCommand;

defined('C5_EXECUTE') or die("Access Denied.");

class GenerateSitemapController extends AbstractController
{

    public function getName(): string
    {
        return t('Generate Sitemap');
    }

    public function getDescription(): string
    {
        return t('Creates sitemap.xml at the root of your site.');
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        return new ProcessTaskRunner($task, new GenerateSitemapCommand(), $input, t('Generation of sitemap.xml started.'));
    }



}
