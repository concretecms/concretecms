<?php

declare(strict_types=1);

namespace Concrete\Tests\Console\Command;

use Concrete\Core\Console\Command\GenerateSitemapCommand;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Sitemap\PageListGenerator;
use Concrete\Core\Page\Sitemap\SitemapGenerator;
use Concrete\Core\Page\Sitemap\SitemapWriter;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests for the c5:sitemap:generate CLI command.
 *
 * Application::getFacadeApplication() resolves the same container that
 * \Core::getFacadeApplication() returns, so binding mocks there before running
 * the command is sufficient — no static facade patching needed.
 */
class GenerateSitemapCommandTest extends TestCase
{
    /**
     * @var string[] abstracts bound during a test that must be unbound on teardown
     */
    private $boundAbstracts = [];

    protected function tearDown(): void
    {
        $app = \Core::getFacadeApplication();
        foreach ($this->boundAbstracts as $abstract) {
            $app->forgetInstance($abstract);
        }
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function bind(string $abstract, object $mock): void
    {
        \Core::getFacadeApplication()->instance($abstract, $mock);
        $this->boundAbstracts[] = $abstract;
    }

    private function makeWriter(?SitemapGenerator $generator = null): SitemapWriter
    {
        $plg = \Mockery::mock(PageListGenerator::class);
        $plg->shouldReceive('getApproximatePageCount')->andReturn(0);

        $gen = $generator ?? \Mockery::mock(SitemapGenerator::class);
        $gen->shouldReceive('getPageListGenerator')->andReturn($plg);

        $writer = \Mockery::mock(SitemapWriter::class);
        $writer->shouldReceive('getSitemapGenerator')->andReturn($gen);

        return $writer;
    }

    private function makeSite(string $canonicalUrl, string $handle = 'default'): Site
    {
        $site = \Mockery::mock(Site::class);
        $site->shouldReceive('getSiteCanonicalURL')->andReturn($canonicalUrl);
        $site->shouldReceive('getSiteHandle')->andReturn($handle);

        return $site;
    }

    /**
     * Run the command and return the CommandTester so callers can assert exit
     * code and output.
     *
     * @param array<string,string> $options
     */
    private function executeCommand(array $options = []): CommandTester
    {
        $command = new GenerateSitemapCommand();
        $console = new ConsoleApplication();
        $console->add($command);

        $tester = new CommandTester($command);
        $tester->execute($options);

        return $tester;
    }

    // -------------------------------------------------------------------------
    // --site path — success cases
    // -------------------------------------------------------------------------

    public function testSiteHandleCallsGenerateForSiteAndPrintsFilename(): void
    {
        $site = $this->makeSite('https://example.com', 'default');

        $writer = $this->makeWriter();
        $writer->shouldReceive('setOutputFilename')->never();
        $writer->shouldReceive('generateForSite')
            ->once()
            ->with($site, '', \Mockery::type('callable'))
        ;
        $writer->shouldReceive('getSitemapUrlForSite')
            ->with($site)
            ->andReturn('https://example.com/sitemap-default.xml')
        ;

        $siteService = \Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getByHandle')->with('default')->andReturn($site);

        $this->bind(SitemapWriter::class, $writer);
        $this->bind(SiteService::class, $siteService);

        $tester = $this->executeCommand(['--site' => 'default']);

        static::assertSame(0, $tester->getStatusCode());
        static::assertStringContainsString('sitemap-default.xml', $tester->getDisplay());
        static::assertStringContainsString('https://example.com/sitemap-default.xml', $tester->getDisplay());
    }

    public function testSiteHandleWithUrlOverridePassesOverrideToWriter(): void
    {
        $site = $this->makeSite('', 'nourl');

        $writer = $this->makeWriter();
        $writer->shouldReceive('generateForSite')
            ->once()
            ->with($site, 'https://override.example.com', \Mockery::type('callable'))
        ;
        $writer->shouldReceive('getSitemapUrlForSite')->andReturn('https://override.example.com/sitemap-nourl.xml');

        $siteService = \Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getByHandle')->with('nourl')->andReturn($site);

        $this->bind(SitemapWriter::class, $writer);
        $this->bind(SiteService::class, $siteService);

        $tester = $this->executeCommand(['--site' => 'nourl', '--url' => 'https://override.example.com']);

        static::assertSame(0, $tester->getStatusCode());
    }

    public function testSiteHandleWithCustomOutputFilenameCallsSetOutputFilename(): void
    {
        $site = $this->makeSite('https://example.com', 'default');

        $writer = $this->makeWriter();
        $writer->shouldReceive('setOutputFilename')->once()->with('/tmp/sitemap-custom.xml');
        $writer->shouldReceive('generateForSite')
            ->once()
            ->with($site, '', \Mockery::type('callable'))
        ;
        $writer->shouldReceive('getSitemapUrlForSite')->andReturn('https://example.com/sitemap-default.xml');

        $siteService = \Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getByHandle')->with('default')->andReturn($site);

        $this->bind(SitemapWriter::class, $writer);
        $this->bind(SiteService::class, $siteService);

        $tester = $this->executeCommand(['--site' => 'default', '--output' => '/tmp/sitemap-custom.xml']);

        static::assertSame(0, $tester->getStatusCode());
        static::assertStringContainsString('sitemap-custom.xml', $tester->getDisplay());
    }

    // -------------------------------------------------------------------------
    // --site path — error cases
    // -------------------------------------------------------------------------

    public function testSiteHandleUnknownThrowsUserMessageException(): void
    {
        $siteService = \Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getByHandle')->with('nonexistent')->andReturn(null);

        $writer = $this->makeWriter();

        $this->bind(SitemapWriter::class, $writer);
        $this->bind(SiteService::class, $siteService);

        $this->expectException(UserMessageException::class);
        $this->expectExceptionMessageMatches('/nonexistent/');

        $this->executeCommand(['--site' => 'nonexistent']);
    }

    public function testSiteHandleWithNoCanonicalUrlAndNoUrlOptionThrowsException(): void
    {
        $site = $this->makeSite('', 'nourl');

        $siteService = \Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getByHandle')->with('nourl')->andReturn($site);

        $writer = $this->makeWriter();

        $this->bind(SitemapWriter::class, $writer);
        $this->bind(SiteService::class, $siteService);

        $this->expectException(UserMessageException::class);
        $this->expectExceptionMessageMatches('/canonical URL/i');

        $this->executeCommand(['--site' => 'nourl']);
    }

    // -------------------------------------------------------------------------
    // No --site (deprecated) path — success cases
    // -------------------------------------------------------------------------

    public function testNoSiteOptionUsesActiveSiteAndLegacyFilename(): void
    {
        $site = $this->makeSite('https://example.com', 'default');
        $legacyFilename = DIR_BASE . '/sitemap.xml';

        $writer = $this->makeWriter();
        $writer->shouldReceive('getOutputFilename')->andReturn($legacyFilename);
        $writer->shouldReceive('setOutputFilename')->once()->with($legacyFilename);
        $writer->shouldReceive('generateForSite')
            ->once()
            ->with($site, '', \Mockery::type('callable'))
        ;

        $siteService = \Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getSite')->once()->andReturn($site);

        $this->bind(SitemapWriter::class, $writer);
        $this->bind(SiteService::class, $siteService);

        $tester = $this->executeCommand([]);

        static::assertSame(0, $tester->getStatusCode());
        static::assertStringContainsString('sitemap.xml', $tester->getDisplay());
    }

    public function testNoSiteOptionWithUrlOverrideUsesOverrideInOutput(): void
    {
        $site = $this->makeSite('', 'default');
        $legacyFilename = DIR_BASE . '/sitemap.xml';

        $writer = $this->makeWriter();
        $writer->shouldReceive('getOutputFilename')->andReturn($legacyFilename);
        $writer->shouldReceive('setOutputFilename')->once()->with($legacyFilename);
        $writer->shouldReceive('generateForSite')
            ->once()
            ->with($site, 'https://override.example.com', \Mockery::type('callable'))
        ;

        $siteService = \Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getSite')->andReturn($site);

        $this->bind(SitemapWriter::class, $writer);
        $this->bind(SiteService::class, $siteService);

        $tester = $this->executeCommand(['--url' => 'https://override.example.com']);

        static::assertSame(0, $tester->getStatusCode());
        static::assertStringContainsString('https://override.example.com/sitemap.xml', $tester->getDisplay());
    }

    // -------------------------------------------------------------------------
    // No --site (deprecated) path — error cases
    // -------------------------------------------------------------------------

    public function testNoSiteOptionWithNullActiveSiteThrowsException(): void
    {
        $siteService = \Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getSite')->andReturn(null);

        $writer = $this->makeWriter();

        $this->bind(SitemapWriter::class, $writer);
        $this->bind(SiteService::class, $siteService);

        $this->expectException(UserMessageException::class);
        $this->expectExceptionMessageMatches('/active site/i');

        $this->executeCommand([]);
    }

    public function testNoSiteOptionWithNoCanonicalUrlAndNoUrlOptionThrowsException(): void
    {
        $site = $this->makeSite('', 'default');

        $siteService = \Mockery::mock(SiteService::class);
        $siteService->shouldReceive('getSite')->andReturn($site);

        $writer = $this->makeWriter();
        $writer->shouldReceive('getOutputFilename')->andReturn(DIR_BASE . '/sitemap.xml');

        $this->bind(SitemapWriter::class, $writer);
        $this->bind(SiteService::class, $siteService);

        $this->expectException(UserMessageException::class);
        $this->expectExceptionMessageMatches('/canonical URL/i');

        $this->executeCommand([]);
    }
}
