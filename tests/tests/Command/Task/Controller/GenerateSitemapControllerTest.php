<?php

namespace Concrete\Tests\Command\Task\Controller;

use Concrete\Core\Command\Task\Controller\GenerateSitemapController;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Sitemap\Command\GenerateSitemapCommand;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Tests\TestCase;
use Mockery;

/**
 * Covers GenerateSitemapController::getTaskRunner() batch composition.
 */
class GenerateSitemapControllerTest extends TestCase
{
    public function testBatchContainsOneSitemapCommandPerSite(): void
    {
        $site1 = $this->makeSite(10);
        $site2 = $this->makeSite(20);

        $siteService = Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getList')->andReturn([$site1, $site2]);

        $controller = new GenerateSitemapController($siteService);
        $runner = $controller->getTaskRunner(
            Mockery::mock(TaskInterface::class),
            Mockery::mock(InputInterface::class)
        );

        $this->assertInstanceOf(BatchProcessTaskRunner::class, $runner);

        $messages = [];
        foreach ($runner->getBatch()->getMessages() as $msg) {
            $messages[] = $msg;
        }

        $this->assertCount(2, $messages);
        $this->assertInstanceOf(GenerateSitemapCommand::class, $messages[0]);
        $this->assertSame(10, $messages[0]->getSiteID());
        $this->assertInstanceOf(GenerateSitemapCommand::class, $messages[1]);
        $this->assertSame(20, $messages[1]->getSiteID());
    }

    public function testEmptySiteListProducesEmptyBatch(): void
    {
        $siteService = Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getList')->andReturn([]);

        $controller = new GenerateSitemapController($siteService);
        $runner = $controller->getTaskRunner(
            Mockery::mock(TaskInterface::class),
            Mockery::mock(InputInterface::class)
        );

        $messages = [];
        foreach ($runner->getBatch()->getMessages() as $msg) {
            $messages[] = $msg;
        }
        $this->assertCount(0, $messages);
    }

    private function makeSite(int $id): Site
    {
        $site = Mockery::mock(Site::class);
        $site->shouldReceive('getSiteID')->andReturn($id);
        return $site;
    }
}
