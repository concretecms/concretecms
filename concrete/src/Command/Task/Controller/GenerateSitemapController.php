<?php

declare(strict_types=1);

namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Page\Sitemap\Command\GenerateSitemapCommand;
use Concrete\Core\Site\Service as SiteService;

defined('C5_EXECUTE') or die('Access Denied.');

class GenerateSitemapController extends AbstractController
{
    /** @var SiteService */
    protected $siteService;

    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return t('Generate Sitemap');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return t('Generates XML sitemaps for all sites.');
    }

    /**
     * {@inheritdoc}
     */
    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $batch = Batch::create();
        foreach ($this->siteService->getList() as $site) {
            $batch->add(new GenerateSitemapCommand($site->getSiteID()));
        }

        return new BatchProcessTaskRunner($task, $batch, $input, t('Generating sitemaps...'));
    }
}
