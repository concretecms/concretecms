<?php

namespace Concrete\Tests\Page\Sitemap;

use Concrete\Core\Config\Repository\Repository as ConfigRepository;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Sitemap\Element\SitemapPage;
use Concrete\Core\Page\Sitemap\PageListGenerator;
use Concrete\Core\Page\Sitemap\SitemapGenerator;
use Concrete\Tests\TestCase;
use League\Url\Url;
use Mockery;

/**
 * Covers SitemapGenerator::generateForSite(), urlHostMatchesCanonical(), and
 * resolveCanonicalHost() — the core multisite correctness invariants introduced
 * on the feature/sitemap-multisite-canonical branch.
 */
class SitemapGeneratorTest extends TestCase
{
    // -------------------------------------------------------------------------
    // urlHostMatchesCanonical — unit tests, no app/DB needed
    // -------------------------------------------------------------------------

    /** @dataProvider provideHostMatchCases */
    public function testUrlHostMatchesCanonical(string $url, string $canonicalHost, bool $expected): void
    {
        $gen = new ExposedSitemapGenerator(
            \Core::getFacadeApplication(),
            \Core::getFacadeApplication()->make('config')
        );

        $this->assertSame($expected, $gen->callUrlHostMatchesCanonical($url, $canonicalHost));
    }

    public function provideHostMatchCases(): array
    {
        return [
            'same host'                    => ['https://example.com/page', 'example.com', true],
            'same host, case-insensitive'  => ['https://EXAMPLE.COM/page', 'example.com', true],
            'different host'               => ['https://other.com/page', 'example.com', false],
            'relative URL (no host)'       => ['/some/path', 'example.com', true],
            'path-only URL'                => ['some/path', 'example.com', true],
            'empty URL'                    => ['', 'example.com', true],
        ];
    }

    // -------------------------------------------------------------------------
    // generateForSite — throws without canonical URL
    // -------------------------------------------------------------------------

    public function testGenerateForSiteThrowsWhenSiteHasNoCanonicalUrlAndNoOverride(): void
    {
        $gen = $this->makeGenerator();
        $site = $this->makeSite('');
        $gen->setPageListGenerator($this->makePageListGenerator([], $site));

        $this->expectException(\RuntimeException::class);
        iterator_to_array($gen->generateForSite($site));
    }

    public function testGenerateForSiteDoesNotThrowWhenOverrideSupplied(): void
    {
        $gen = $this->makeGenerator();
        $site = $this->makeSite('');
        $gen->setPageListGenerator($this->makePageListGenerator([], $site));

        // Site has no canonical URL but override is provided — should not throw.
        $elements = iterator_to_array(
            $gen->generateForSite($site, 'https://override.example.com')
        );

        $this->assertNotEmpty($elements);
    }

    public function testGenerateForSitePrefersOverrideOverSiteCanonicalUrl(): void
    {
        $gen = $this->makeGenerator();
        $gen->setNextPageUrl('https://override.example.com/page');
        $site = $this->makeSite('https://site.example.com');
        $gen->setPageListGenerator($this->makePageListGenerator([$this->makePage()], $site));

        // Strict mode (default): if the override were ignored and site URL were used,
        // the page would fail host validation and throw. Passing means override was used.
        $elements = iterator_to_array(
            $gen->generateForSite($site, 'https://override.example.com')
        );

        $pages = array_filter($elements, fn($e) => $e instanceof SitemapPage);
        $this->assertCount(1, $pages);
    }

    // -------------------------------------------------------------------------
    // Host-purity invariant — strict vs lenient
    // -------------------------------------------------------------------------

    public function testStrictModeThrowsWhenPageUrlEscapesCanonicalHost(): void
    {
        $gen = $this->makeGenerator();
        $gen->setNextPageUrl('https://other-host.com/page');
        $site = $this->makeSite('https://canonical.example.com');
        $gen->setPageListGenerator($this->makePageListGenerator([$this->makePage()], $site));

        $this->assertTrue($gen->isStrictCanonicalHost());
        $this->expectException(\RuntimeException::class);

        iterator_to_array($gen->generateForSite($site));
    }

    public function testLenientModeSkipsPageWithWrongHost(): void
    {
        $gen = $this->makeGenerator();
        $gen->setNextPageUrl('https://other-host.com/page');
        $gen->setStrictCanonicalHost(false);
        $site = $this->makeSite('https://canonical.example.com');
        $gen->setPageListGenerator($this->makePageListGenerator([$this->makePage()], $site));

        $elements = iterator_to_array($gen->generateForSite($site));

        $pages = array_filter($elements, fn($e) => $e instanceof SitemapPage);
        $this->assertCount(0, $pages, 'Cross-host page should be silently dropped in lenient mode');
    }

    public function testCorrectHostPageIsIncluded(): void
    {
        $gen = $this->makeGenerator();
        $gen->setNextPageUrl('https://canonical.example.com/page');
        $site = $this->makeSite('https://canonical.example.com');
        $gen->setPageListGenerator($this->makePageListGenerator([$this->makePage()], $site));

        $elements = iterator_to_array($gen->generateForSite($site));

        $pages = array_filter($elements, fn($e) => $e instanceof SitemapPage);
        $this->assertCount(1, $pages);
    }

    // -------------------------------------------------------------------------
    // generateForSite — state management
    // -------------------------------------------------------------------------

    public function testGenerateForSiteRestoresCanonicalUrlOnSuccess(): void
    {
        $gen = $this->makeGenerator();
        $gen->setCustomSiteCanonicalUrl('https://before.example.com');
        $site = $this->makeSite('https://site.example.com');
        $gen->setPageListGenerator($this->makePageListGenerator([], $site));

        iterator_to_array($gen->generateForSite($site));

        $this->assertSame('https://before.example.com', $gen->getCustomSiteCanonicalUrl());
    }

    public function testGenerateForSiteRestoresCanonicalUrlOnException(): void
    {
        $gen = $this->makeGenerator();
        $gen->setCustomSiteCanonicalUrl('https://before.example.com');
        $gen->setNextPageUrl('https://wrong-host.com/page');
        $site = $this->makeSite('https://site.example.com');
        $gen->setPageListGenerator($this->makePageListGenerator([$this->makePage()], $site));

        try {
            iterator_to_array($gen->generateForSite($site));
        } catch (\RuntimeException $e) {
            // expected
        }

        $this->assertSame(
            'https://before.example.com',
            $gen->getCustomSiteCanonicalUrl(),
            'Custom canonical URL must be restored even after an exception'
        );
    }

    public function testGenerateForSiteRestoresSiteOnPageListGeneratorOnSuccess(): void
    {
        $prevSite = $this->makeSite('https://prev.example.com', 'prev');
        $site     = $this->makeSite('https://site.example.com', 'site');

        $setSiteCalls = [];
        $plg = Mockery::mock(PageListGenerator::class);
        $plg->shouldReceive('getSite')->andReturn($prevSite);
        $plg->shouldReceive('setSite')
            ->andReturnUsing(function ($s) use (&$setSiteCalls, $plg) {
                $setSiteCalls[] = $s;
                return $plg;
            });
        $plg->shouldReceive('isMultilingualEnabled')->andReturn(false);
        $plg->shouldReceive('generatePageList')->andReturn((function () { yield from []; })());

        $gen = $this->makeGenerator();
        $gen->setPageListGenerator($plg);

        iterator_to_array($gen->generateForSite($site));

        $this->assertCount(2, $setSiteCalls, 'setSite must be called twice: forward and restore');
        $this->assertSame($site, $setSiteCalls[0], 'First call binds the requested site');
        $this->assertSame($prevSite, $setSiteCalls[1], 'Second call restores the previous site');
    }

    public function testGenerateForSiteRestoresSiteOnPageListGeneratorOnException(): void
    {
        $prevSite   = $this->makeSite('https://prev.example.com', 'prev');
        $noUrlSite  = $this->makeSite('', 'nourl');

        $setSiteCalls = [];
        $plg = Mockery::mock(PageListGenerator::class);
        $plg->shouldReceive('getSite')->andReturn($prevSite);
        $plg->shouldReceive('setSite')
            ->andReturnUsing(function ($s) use (&$setSiteCalls, $plg) {
                $setSiteCalls[] = $s;
                return $plg;
            });

        $gen = $this->makeGenerator();
        $gen->setPageListGenerator($plg);

        try {
            iterator_to_array($gen->generateForSite($noUrlSite));
        } catch (\RuntimeException $e) {
            // expected — no canonical URL
        }

        $this->assertCount(2, $setSiteCalls, 'setSite must be called twice even when an exception is thrown');
        $this->assertSame($noUrlSite, $setSiteCalls[0]);
        $this->assertSame($prevSite, $setSiteCalls[1]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeGenerator(): ExposedSitemapGenerator
    {
        $app = \Core::getFacadeApplication();
        return new ExposedSitemapGenerator($app, $app->make('config'));
    }

    private function makeSite(string $canonicalUrl, string $handle = 'default', int $id = 1): Site
    {
        // generateContents() calls getSite()->getConfigRepository() to temporarily
        // swap seo.canonical_url, so the mock must support it.
        $siteConfig = Mockery::mock(ConfigRepository::class);
        $siteConfig->shouldReceive('get')->andReturn('');
        $siteConfig->shouldReceive('set');

        $site = Mockery::mock(Site::class);
        $site->shouldReceive('getSiteCanonicalURL')->andReturn($canonicalUrl);
        $site->shouldReceive('getSiteHandle')->andReturn($handle);
        $site->shouldReceive('getSiteID')->andReturn($id);
        $site->shouldReceive('getConfigRepository')->andReturn($siteConfig);
        return $site;
    }

    private function makePageListGenerator(array $pages = [], ?Site $site = null): PageListGenerator
    {
        $plg = Mockery::mock(PageListGenerator::class);
        $plg->shouldReceive('setSite')->andReturn($plg);
        $plg->shouldReceive('getSite')->andReturn($site);
        $plg->shouldReceive('isMultilingualEnabled')->andReturn(false);
        $plg->shouldReceive('generatePageList')->andReturn((function () use ($pages) {
            yield from $pages;
        })());
        return $plg;
    }

    private function makePage(): Page
    {
        $page = Mockery::mock(Page::class);
        $page->shouldReceive('getCollectionDateLastModified')->andReturn(null);
        $page->shouldReceive('getCollectionID')->andReturn(1);
        return $page;
    }
}

/**
 * Exposes protected methods and injects controllable page URLs for testing.
 */
class ExposedSitemapGenerator extends SitemapGenerator
{
    private string $nextPageUrl = 'https://canonical.example.com/page';

    public function setNextPageUrl(string $url): void
    {
        $this->nextPageUrl = $url;
    }

    public function callUrlHostMatchesCanonical(string $url, string $canonicalHost): bool
    {
        return $this->urlHostMatchesCanonical($url, $canonicalHost);
    }

    protected function createSitemapPage(Page $page, $multilingualEnabled): SitemapPage
    {
        return new SitemapPage($page, Url::createFromUrl($this->nextPageUrl));
    }

    // Avoid DB calls for attribute keys — not what these tests are measuring.
    protected function getSitemapChangeFrequencyAttributeKey()
    {
        return null;
    }

    protected function getSitemapPriorityAttributeKey()
    {
        return null;
    }
}
