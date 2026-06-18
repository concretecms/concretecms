<?php

declare(strict_types=1);

namespace Concrete\Tests\Page\Sitemap;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Page\Sitemap\PageListGenerator;
use Concrete\Core\Page\Sitemap\SitemapGenerator;
use Concrete\Core\Page\Sitemap\SitemapWriter;
use Concrete\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Covers SitemapWriter::getOutputFilenameForSite(), getSitemapUrlForSite(),
 * and the state-management contract of generateForSite().
 */
class SitemapWriterTest extends TestCase
{
    // -------------------------------------------------------------------------
    // getOutputFilenameForSite — pure static computation
    // -------------------------------------------------------------------------

    /**
     * @dataProvider provideHandleAndExpectedPath
     */
    public function testGetOutputFilenameForSiteReturnsCorrectPath(
        string $handle,
        string $expectedSuffix
    ): void {
        $site = $this->makeSite('https://example.com', $handle);
        $path = SitemapWriter::getOutputFilenameForSite($site);

        static::assertStringEndsWith($expectedSuffix, $path);
        static::assertStringStartsWith(DIR_BASE, $path);
    }

    public static function provideHandleAndExpectedPath(): array
    {
        return [
            'default site' => ['default', '/sitemap-default.xml'],
            'secondary site' => ['microsite', '/sitemap-microsite.xml'],
            'hyphenated' => ['my-site', '/sitemap-my-site.xml'],
        ];
    }

    // -------------------------------------------------------------------------
    // getSitemapUrlForSite
    // -------------------------------------------------------------------------

    public function testGetSitemapUrlForSiteReturnsCanonicalPrefixedPath(): void
    {
        $site = $this->makeSite('https://example.com', 'default');
        $writer = $this->makeWriter();

        $url = $writer->getSitemapUrlForSite($site);

        static::assertSame('https://example.com/sitemap-default.xml', $url);
    }

    public function testGetSitemapUrlForSiteStripsTrailingSlashFromCanonicalUrl(): void
    {
        $site = $this->makeSite('https://example.com/', 'store');
        $writer = $this->makeWriter();

        $url = $writer->getSitemapUrlForSite($site);

        static::assertSame('https://example.com/sitemap-store.xml', $url);
    }

    // -------------------------------------------------------------------------
    // generateForSite — throws without canonical URL
    // -------------------------------------------------------------------------

    public function testGenerateForSiteThrowsWhenSiteHasNoCanonicalUrlAndNoOverride(): void
    {
        $writer = $this->makeWriter();
        $site = $this->makeSite('', 'default');

        $this->expectException(\RuntimeException::class);
        $writer->generateForSite($site);
    }

    // -------------------------------------------------------------------------
    // generateForSite — state management
    // -------------------------------------------------------------------------

    public function testGenerateForSiteSetsOutputFilenameToSiteSpecificPathDuringGeneration(): void
    {
        $site = $this->makeSite('https://example.com', 'mysite');
        $writer = $this->makeCapturingWriter();

        $writer->generateForSite($site);

        static::assertStringEndsWith(
            '/sitemap-mysite.xml',
            $writer->capturedOutputFilename,
            'Writer should use the site-specific filename during generation'
        );
    }

    public function testGenerateForSiteRestoresOutputFilenameAfterGeneration(): void
    {
        $site = $this->makeSite('https://example.com', 'mysite');
        $writer = $this->makeCapturingWriter();
        $writer->setOutputFilename('/custom/path/sitemap.xml');

        $writer->generateForSite($site);

        // The custom filename should be restored after generateForSite() exits.
        static::assertSame('/custom/path/sitemap.xml', $writer->getOutputFilename());
    }

    public function testGenerateForSiteRestoresGeneratorStateAfterGeneration(): void
    {
        $site = $this->makeSite('https://example.com', 'default');
        $writer = $this->makeCapturingWriter();

        $gen = $writer->getSitemapGenerator();
        $gen->setCustomSiteCanonicalUrl('https://before.example.com');

        $writer->generateForSite($site);

        static::assertSame(
            'https://before.example.com',
            $gen->getCustomSiteCanonicalUrl(),
            'Generator canonical URL must be restored after generateForSite()'
        );
    }

    public function testGenerateForSiteRestoresGeneratorStateEvenOnException(): void
    {
        // A site with no canonical URL will throw inside generateForSite() before
        // generating — but only after state has been mutated. Verify restoration.
        $site = $this->makeSite('', 'default');
        $writer = $this->makeCapturingWriter();

        $gen = $writer->getSitemapGenerator();
        $gen->setCustomSiteCanonicalUrl('https://before.example.com');

        try {
            $writer->generateForSite($site);
        } catch (\RuntimeException $e) {
            // expected
        }

        static::assertSame(
            'https://before.example.com',
            $gen->getCustomSiteCanonicalUrl(),
            'Generator canonical URL must be restored even after an exception'
        );
    }

    public function testGenerateForSiteUsesCanonicalUrlOverrideInsteadOfSiteCanonicalUrl(): void
    {
        $site = $this->makeSite('https://site.example.com', 'mysite');
        $writer = $this->makeCapturingWriter();

        $writer->generateForSite($site, 'https://override.example.com');

        static::assertSame(
            'https://override.example.com',
            $writer->capturedCanonicalUrl,
            'The override URL must be used during generation, not the site canonical URL'
        );
    }

    public function testGenerateForSiteSkipsSiteRestoreWhenPreviousSiteWasNull(): void
    {
        // PageListGenerator::setSite() has a non-nullable type hint, so calling
        // setSite(null) would throw a TypeError. When getSite() returned null before
        // the call, the finally block must skip the restore rather than crash.
        $site = $this->makeSite('https://example.com', 'mysite');
        $writer = $this->makeCapturingWriter();

        $setSiteCalls = [];
        $plg = \Mockery::mock(PageListGenerator::class);
        $plg->shouldReceive('getSite')->andReturn(null);
        $plg->shouldReceive('setSite')
            ->andReturnUsing(static function ($s) use (&$setSiteCalls, $plg) {
                $setSiteCalls[] = $s;

                return $plg;
            })
        ;

        $gen = \Mockery::mock(SitemapGenerator::class);
        $gen->shouldReceive('getPageListGenerator')->andReturn($plg);
        $gen->shouldReceive('getCustomSiteCanonicalUrl')->andReturn('');
        $gen->shouldReceive('setCustomSiteCanonicalUrl');

        $writer->setSitemapGenerator($gen);
        $writer->generateForSite($site); // must not throw TypeError

        static::assertCount(1, $setSiteCalls, 'Only the forward setSite call should occur; restore is skipped for null');
        static::assertSame($site, $setSiteCalls[0]);
    }

    public function testGenerateForSiteRespectsCallerSuppliedOutputFilename(): void
    {
        $site = $this->makeSite('https://example.com', 'mysite');
        $writer = $this->makeCapturingWriter();
        $writer->setOutputFilename('/custom/sitemap.xml');

        $writer->generateForSite($site);

        static::assertSame(
            '/custom/sitemap.xml',
            $writer->capturedOutputFilename,
            'When caller pre-sets an output filename, generateForSite() should not override it'
        );
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeSite(string $canonicalUrl, string $handle = 'default'): Site
    {
        $site = \Mockery::mock(Site::class);
        $site->shouldReceive('getSiteCanonicalURL')->andReturn($canonicalUrl);
        $site->shouldReceive('getSiteHandle')->andReturn($handle);

        return $site;
    }

    private function makeWriter(): SitemapWriter
    {
        $app = \Core::getFacadeApplication();

        return new SitemapWriter($app, new Filesystem(), $app->make(EventDispatcher::class));
    }

    private function makeCapturingWriter(): CapturingSitemapWriter
    {
        $app = \Core::getFacadeApplication();

        return new CapturingSitemapWriter($app, new Filesystem(), $app->make(EventDispatcher::class));
    }
}

/**
 * Overrides generate() so no actual file I/O occurs, and captures the output
 * filename in effect at the moment generate() is called.
 */
class CapturingSitemapWriter extends SitemapWriter
{
    /**
     * @var string
     */
    public $capturedOutputFilename = '';

    /**
     * @var string
     */
    public $capturedCanonicalUrl = '';

    public function generate(?callable $pulse = null)
    {
        $this->capturedOutputFilename = $this->getOutputFilename();
        $this->capturedCanonicalUrl = $this->getSitemapGenerator()->getCustomSiteCanonicalUrl();
    }
}
