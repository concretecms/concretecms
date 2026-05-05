<?php

namespace Concrete\Tests\Page\Sitemap;

use Concrete\Core\Command\Task\Output\NullOutput;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Sitemap\Command\GenerateRobotsTxtCommand;
use Concrete\Core\Page\Sitemap\Command\GenerateRobotsTxtCommandHandler;
use Concrete\Core\Page\Sitemap\Command\GenerateSitemapCommand;
use Concrete\Core\Page\Sitemap\Command\GenerateSitemapCommandHandler;
use Concrete\Core\Page\Sitemap\SitemapWriter;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Core\Site\WellKnown\WellKnownFileManager;
use Concrete\Tests\TestCase;
use Mockery;

/**
 * Covers GenerateSitemapCommand/Handler and GenerateRobotsTxtCommand/Handler.
 */
class GenerateSitemapCommandTest extends TestCase
{
    // -------------------------------------------------------------------------
    // GenerateSitemapCommand — value object
    // -------------------------------------------------------------------------

    public function testCommandHasNullSiteIdByDefault(): void
    {
        $command = new GenerateSitemapCommand();
        $this->assertNull($command->getSiteID());
    }

    public function testCommandCarriesSiteId(): void
    {
        $command = new GenerateSitemapCommand(42);
        $this->assertSame(42, $command->getSiteID());
    }

    public function testCommandWithZeroSiteIdIsDistinctFromNull(): void
    {
        $command = new GenerateSitemapCommand(0);
        $this->assertSame(0, $command->getSiteID());
        $this->assertNotNull($command->getSiteID());
    }

    // -------------------------------------------------------------------------
    // GenerateSitemapCommandHandler — routing
    // -------------------------------------------------------------------------

    public function testHandlerCallsGenerateForSiteWhenSiteIdIsSet(): void
    {
        $site = Mockery::mock(Site::class);
        $site->shouldReceive('getSiteCanonicalURL')->andReturn('https://example.com');
        $site->shouldReceive('getSiteHandle')->andReturn('default');

        $siteService = Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getByID')->with(7)->andReturn($site);

        $expectedPath = sys_get_temp_dir() . '/wkm_test_' . getmypid() . '/sitemap.xml';

        $writer = Mockery::mock(SitemapWriter::class);
        $writer->shouldReceive('setOutputFilename')->once()->with($expectedPath);
        $writer->shouldReceive('generateForSite')
            ->once()
            ->with($site, '', Mockery::type('callable'));

        $wellKnown = Mockery::mock(WellKnownFileManager::class);
        $wellKnown->shouldReceive('getFilePath')->once()->with($site, 'sitemap.xml')->andReturn($expectedPath);

        $handler = new GenerateSitemapCommandHandler($writer, $siteService, $wellKnown);
        $handler->setOutput(new NullOutput());
        $handler->__invoke(new GenerateSitemapCommand(7));
    }

    public function testHandlerWritesDirectlyToModeAwarePath(): void
    {
        $site = Mockery::mock(Site::class);
        $site->shouldReceive('getSiteCanonicalURL')->andReturn('https://example.com');
        $site->shouldReceive('getSiteHandle')->andReturn('writetest');

        $siteService = Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getByID')->with(5)->andReturn($site);

        $tmpDir = sys_get_temp_dir() . '/wkm_test_' . getmypid();
        $expectedPath = $tmpDir . '/sitemap.xml';

        $writer = Mockery::mock(SitemapWriter::class);
        // Handler must set the output path before delegating to the writer — no copy step.
        $writer->shouldReceive('setOutputFilename')->once()->with($expectedPath);
        $writer->shouldReceive('generateForSite')->once();

        $wellKnown = Mockery::mock(WellKnownFileManager::class);
        $wellKnown->shouldReceive('getFilePath')
            ->once()
            ->with($site, 'sitemap.xml')
            ->andReturn($expectedPath);

        $handler = new GenerateSitemapCommandHandler($writer, $siteService, $wellKnown);
        $handler->setOutput(new NullOutput());
        $handler->__invoke(new GenerateSitemapCommand(5));
    }

    public function testHandlerThrowsWhenSiteIdNotFound(): void
    {
        $siteService = Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getByID')->with(99)->andReturn(null);

        $writer = Mockery::mock(SitemapWriter::class);
        $wellKnown = Mockery::mock(WellKnownFileManager::class);

        $handler = new GenerateSitemapCommandHandler($writer, $siteService, $wellKnown);
        $handler->setOutput(new NullOutput());

        $this->expectException(\RuntimeException::class);
        $handler->__invoke(new GenerateSitemapCommand(99));
    }

    public function testHandlerThrowsWhenLegacySiteIsNull(): void
    {
        $siteService = Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getSite')->once()->andReturn(null);

        $writer = Mockery::mock(SitemapWriter::class);
        $wellKnown = Mockery::mock(WellKnownFileManager::class);

        $handler = new GenerateSitemapCommandHandler($writer, $siteService, $wellKnown);
        $handler->setOutput(new NullOutput());

        $this->expectException(\RuntimeException::class);
        $handler->__invoke(new GenerateSitemapCommand());
    }

    public function testHandlerCallsLegacyGenerateWhenSiteIdIsNull(): void
    {
        $site = Mockery::mock(Site::class);
        $site->shouldReceive('getSiteCanonicalURL')->andReturn('https://example.com');
        $site->shouldReceive('getSiteHandle')->andReturn('default');

        $siteService = Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getSite')->once()->andReturn($site);

        $writer = Mockery::mock(SitemapWriter::class);
        // Null siteID still goes through generateForSite() with the legacy filename pre-set.
        $writer->shouldReceive('getOutputFilename')->andReturn('/var/www/sitemap.xml');
        $writer->shouldReceive('setOutputFilename')->once()->with('/var/www/sitemap.xml');
        $writer->shouldReceive('generateForSite')
            ->once()
            ->with($site, '', Mockery::type('callable'));

        $wellKnown = Mockery::mock(WellKnownFileManager::class);

        $handler = new GenerateSitemapCommandHandler($writer, $siteService, $wellKnown);
        $handler->setOutput(new NullOutput());
        $handler->__invoke(new GenerateSitemapCommand());
    }

    // -------------------------------------------------------------------------
    // GenerateRobotsTxtCommandHandler
    // -------------------------------------------------------------------------

    public function testRobotsTxtHandlerThrowsWhenSiteNotFound(): void
    {
        $siteService = Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getByID')->with(55)->andReturn(null);

        $wellKnown = Mockery::mock(WellKnownFileManager::class);

        $handler = new GenerateRobotsTxtCommandHandler($siteService, $wellKnown);
        $handler->setOutput(new NullOutput());

        $this->expectException(\RuntimeException::class);
        $handler->__invoke(new GenerateRobotsTxtCommand(55));
    }

    public function testRobotsTxtHandlerSkipsWhenNoCanonicalUrl(): void
    {
        $site = Mockery::mock(Site::class);
        $site->shouldReceive('getSiteCanonicalURL')->andReturn('');
        $site->shouldReceive('getSiteHandle')->andReturn('nourl');

        $siteService = Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getByID')->with(3)->andReturn($site);

        $wellKnown = Mockery::mock(WellKnownFileManager::class);
        $wellKnown->shouldNotReceive('writeFile');

        $handler = new GenerateRobotsTxtCommandHandler($siteService, $wellKnown);
        $handler->setOutput(new NullOutput());
        $handler->__invoke(new GenerateRobotsTxtCommand(3));
    }

    public function testRobotsTxtHandlerWritesFileWithSitemapDirective(): void
    {
        $site = Mockery::mock(Site::class);
        $site->shouldReceive('getSiteCanonicalURL')->andReturn('https://example.com');
        $site->shouldReceive('getSiteHandle')->andReturn('default');

        $siteService = Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getByID')->with(1)->andReturn($site);

        $wellKnown = Mockery::mock(WellKnownFileManager::class);
        $wellKnown->shouldReceive('writeFile')
            ->once()
            ->with($site, 'robots.txt', Mockery::on(function (string $content): bool {
                return strpos($content, 'Sitemap: https://example.com/sitemap.xml') !== false;
            }));

        $handler = new GenerateRobotsTxtCommandHandler($siteService, $wellKnown);
        $handler->setOutput(new NullOutput());
        $handler->__invoke(new GenerateRobotsTxtCommand(1));
    }

    public function testRobotsTxtHandlerStripsExistingSitemapDirectives(): void
    {
        // Write a base robots.txt with an existing Sitemap: directive.
        $baseFile = rtrim(DIR_BASE, '/') . '/robots.txt';
        $originalContent = is_file($baseFile) ? file_get_contents($baseFile) : null;
        file_put_contents($baseFile, "User-agent: *\nDisallow: /private\nSitemap: https://old.example.com/sitemap.xml\n");

        $site = Mockery::mock(Site::class);
        $site->shouldReceive('getSiteCanonicalURL')->andReturn('https://new.example.com');
        $site->shouldReceive('getSiteHandle')->andReturn('new');

        $siteService = Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getByID')->with(2)->andReturn($site);

        $capturedContent = '';
        $wellKnown = Mockery::mock(WellKnownFileManager::class);
        $wellKnown->shouldReceive('writeFile')
            ->once()
            ->with($site, 'robots.txt', Mockery::on(function (string $content) use (&$capturedContent): bool {
                $capturedContent = $content;
                return true;
            }));

        $handler = new GenerateRobotsTxtCommandHandler($siteService, $wellKnown);
        $handler->setOutput(new NullOutput());
        $handler->__invoke(new GenerateRobotsTxtCommand(2));

        // Restore base robots.txt
        if ($originalContent === null) {
            @unlink($baseFile);
        } else {
            file_put_contents($baseFile, $originalContent);
        }

        $this->assertStringNotContainsString('https://old.example.com', $capturedContent);
        $this->assertStringContainsString('Sitemap: https://new.example.com/sitemap.xml', $capturedContent);
        // Base rules preserved.
        $this->assertStringContainsString('User-agent: *', $capturedContent);
        $this->assertStringContainsString('Disallow: /private', $capturedContent);
    }

    public function testRobotsTxtHandlerWorksWithNoBaseRobotsTxt(): void
    {
        $site = Mockery::mock(Site::class);
        $site->shouldReceive('getSiteCanonicalURL')->andReturn('https://blank.example.com');
        $site->shouldReceive('getSiteHandle')->andReturn('blank');

        $siteService = Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getByID')->with(4)->andReturn($site);

        // Ensure there is no base robots.txt.
        $baseFile = rtrim(DIR_BASE, '/') . '/robots.txt';
        $originalContent = is_file($baseFile) ? file_get_contents($baseFile) : null;
        if (is_file($baseFile)) {
            @unlink($baseFile);
        }

        $wellKnown = Mockery::mock(WellKnownFileManager::class);
        $wellKnown->shouldReceive('writeFile')
            ->once()
            ->with($site, 'robots.txt', Mockery::on(function (string $content): bool {
                // No base file → content must start directly with "Sitemap:", no leading blank line.
                return strpos($content, 'Sitemap: https://blank.example.com/sitemap.xml') === 0;
            }));

        $handler = new GenerateRobotsTxtCommandHandler($siteService, $wellKnown);
        $handler->setOutput(new NullOutput());
        $handler->__invoke(new GenerateRobotsTxtCommand(4));

        if ($originalContent !== null) {
            file_put_contents($baseFile, $originalContent);
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeSite(string $canonicalUrl, string $handle = 'default'): Site
    {
        $site = Mockery::mock(Site::class);
        $site->shouldReceive('getSiteCanonicalURL')->andReturn($canonicalUrl);
        $site->shouldReceive('getSiteHandle')->andReturn($handle);

        return $site;
    }
}
