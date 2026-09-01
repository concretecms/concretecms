<?php

namespace Concrete\Tests\Site\WellKnown;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Core\Site\WellKnown\WellKnownFileManager;
use Concrete\Tests\TestCase;
use Mockery;

class WellKnownFileManagerTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Instance helper methods
    // -------------------------------------------------------------------------

    public function testGetHostForSiteReturnsLowercasedHost(): void
    {
        $site = $this->makeSite('https://Example.COM/');
        $this->assertSame('example.com', $this->makeManager()->getHostForSite($site));
    }

    public function testGetHostForSiteReturnsEmptyStringWhenNoCanonicalUrl(): void
    {
        $site = $this->makeSite('');
        $this->assertSame('', $this->makeManager()->getHostForSite($site));
    }

    public function testGetHostForSiteStripsPort(): void
    {
        $site = $this->makeSite('https://example.com:8443/');
        // parse_url returns host without port
        $this->assertSame('example.com', $this->makeManager()->getHostForSite($site));
    }

    public function testGetDirectoryForSiteReturnsPathUnderSiteFiles(): void
    {
        $site = $this->makeSite('https://example.com');
        $dir = $this->makeManager()->getDirectoryForSite($site);
        $this->assertStringEndsWith('/application/files/site-specific/example.com', $dir);
        $this->assertStringStartsWith(rtrim(DIR_BASE, '/'), $dir);
    }

    public function testGetDirectoryForSiteReturnsEmptyStringWhenNoCanonicalUrl(): void
    {
        $site = $this->makeSite('');
        $this->assertSame('', $this->makeManager()->getDirectoryForSite($site));
    }

    public function testGetFilePathForSiteComposesCorrectPath(): void
    {
        $site = $this->makeSite('https://example.com');
        $path = $this->makeManager()->getFilePathForSite($site, 'robots.txt');
        $this->assertStringEndsWith('/application/files/site-specific/example.com/robots.txt', $path);
    }

    public function testGetFilePathForSiteRejectsDisallowedFilename(): void
    {
        $site = $this->makeSite('https://example.com');
        $this->assertSame('', $this->makeManager()->getFilePathForSite($site, '../etc/passwd'));
    }

    public function testGetFilePathForSiteReturnsEmptyStringWhenNoCanonicalUrl(): void
    {
        $site = $this->makeSite('');
        $this->assertSame('', $this->makeManager()->getFilePathForSite($site, 'robots.txt'));
    }

    public function testGetUrlForSiteComposesCorrectUrl(): void
    {
        $site = $this->makeSite('https://example.com');
        $this->assertSame('https://example.com/sitemap.xml', $this->makeManager()->getUrlForSite($site, 'sitemap.xml'));
    }

    public function testGetUrlForSiteStripsTrailingSlashFromCanonical(): void
    {
        $site = $this->makeSite('https://example.com/');
        $this->assertSame('https://example.com/robots.txt', $this->makeManager()->getUrlForSite($site, 'robots.txt'));
    }

    public function testGetUrlForSiteReturnsEmptyStringWhenNoCanonicalUrl(): void
    {
        $site = $this->makeSite('');
        $this->assertSame('', $this->makeManager()->getUrlForSite($site, 'robots.txt'));
    }

    public function testGetUrlForSiteMapsSecurityTxtToWellKnownPath(): void
    {
        $site = $this->makeSite('https://example.com');
        $this->assertSame('https://example.com/.well-known/security.txt', $this->makeManager()->getUrlForSite($site, 'security.txt'));
    }

    public function testGetUrlForSiteRejectsDisallowedFilename(): void
    {
        $site = $this->makeSite('https://example.com');
        $this->assertSame('', $this->makeManager()->getUrlForSite($site, '../etc/passwd'));
    }

    // -------------------------------------------------------------------------
    // getFilePath()
    // -------------------------------------------------------------------------

    public function testGetFilePathTreatsOneSiteWithCanonicalUrlAsWebroot(): void
    {
        // Multisite may be enabled at the feature level but if only one site has a
        // canonical URL the system should behave like a single-site install: files
        // land in the webroot and no web-server reconfiguration is needed.
        $siteService = Mockery::mock(SiteService::class);
        $only = Mockery::mock(Site::class);
        $only->shouldReceive('getSiteCanonicalURL')->andReturn('https://example.com');
        $siteService->shouldReceive('getList')->andReturn([$only]);

        $manager = new WellKnownFileManager($siteService);
        $site = $this->makeSite('https://example.com');

        $this->assertSame(rtrim(DIR_BASE, '/') . '/robots.txt', $manager->getFilePath($site, 'robots.txt'));
    }

    public function testGetFilePathTreatsOneSiteWithoutCanonicalUrlAsWebroot(): void
    {
        // A site exists (multisite exploring) but no canonical URL has been set yet.
        // The site contributes 0 to the canonical count, so webroot mode applies.
        $siteService = Mockery::mock(SiteService::class);
        $unconfigured = Mockery::mock(Site::class);
        $unconfigured->shouldReceive('getSiteCanonicalURL')->andReturn('');
        $siteService->shouldReceive('getList')->andReturn([$unconfigured]);

        $manager = new WellKnownFileManager($siteService);
        $site = $this->makeSite('https://example.com');

        $this->assertSame(rtrim(DIR_BASE, '/') . '/robots.txt', $manager->getFilePath($site, 'robots.txt'));
    }

    public function testGetFilePathTreatsPartiallyConfiguredMultisiteAsWebroot(): void
    {
        // Two sites exist but only one has a canonical URL. Per-host routing requires
        // both sites to be reachable at distinct hosts, so we stay in webroot mode
        // until the second site is also configured.
        $siteService = Mockery::mock(SiteService::class);
        $configured = Mockery::mock(Site::class);
        $configured->shouldReceive('getSiteCanonicalURL')->andReturn('https://example.com');
        $unconfigured = Mockery::mock(Site::class);
        $unconfigured->shouldReceive('getSiteCanonicalURL')->andReturn('');
        $siteService->shouldReceive('getList')->andReturn([$configured, $unconfigured]);

        $manager = new WellKnownFileManager($siteService);
        $site = $this->makeSite('https://example.com');

        $this->assertSame(rtrim(DIR_BASE, '/') . '/robots.txt', $manager->getFilePath($site, 'robots.txt'));
    }

    public function testGetFilePathReturnsPerSitePathInMultisiteMode(): void
    {
        $site = $this->makeSite('https://example.com');
        $path = $this->makeManager(true)->getFilePath($site, 'robots.txt');
        $this->assertStringEndsWith('/application/files/site-specific/example.com/robots.txt', $path);
    }

    public function testGetFilePathReturnsSingleSiteWebrootPath(): void
    {
        $site = $this->makeSite('https://example.com');
        $path = $this->makeManager(false)->getFilePath($site, 'robots.txt');
        $this->assertSame(rtrim(DIR_BASE, '/') . '/robots.txt', $path);
    }

    public function testGetFilePathMapsSingleSiteSecurityTxtToWellKnownSubdir(): void
    {
        $site = $this->makeSite('https://example.com');
        $path = $this->makeManager(false)->getFilePath($site, 'security.txt');
        $this->assertSame(rtrim(DIR_BASE, '/') . '/.well-known/security.txt', $path);
    }

    public function testGetFilePathRejectsDisallowedFilename(): void
    {
        $site = $this->makeSite('https://example.com');
        $this->assertSame('', $this->makeManager(false)->getFilePath($site, '../etc/passwd'));
        $this->assertSame('', $this->makeManager(true)->getFilePath($site, '../etc/passwd'));
    }

    // -------------------------------------------------------------------------
    // writeFile()
    // -------------------------------------------------------------------------

    public function testWriteFileCreatesDirectoryAndFile(): void
    {
        $site = $this->makeSite('https://writetest.example.com');
        $manager = $this->makeManager(true);

        $path = $manager->writeFile($site, 'robots.txt', "User-agent: *\nDisallow:\n");

        $this->assertFileExists($path);
        $this->assertStringContainsString('User-agent', (string) file_get_contents($path));

        // Clean up
        @unlink($path);
        @rmdir($manager->getDirectoryForSite($site));
    }

    public function testWriteFileReturnsEmptyStringWhenNoCanonicalUrl(): void
    {
        $site = $this->makeSite('');
        $this->assertSame('', $this->makeManager(true)->writeFile($site, 'robots.txt', 'content'));
    }

    public function testWriteFileRejectsDisallowedFilename(): void
    {
        $site = $this->makeSite('https://example.com');
        $this->assertSame('', $this->makeManager(true)->writeFile($site, '../etc/passwd', 'evil'));
        $this->assertSame('', $this->makeManager(false)->writeFile($site, '../etc/passwd', 'evil'));
    }

    // -------------------------------------------------------------------------
    // Storage mode
    // -------------------------------------------------------------------------

    public function testIsMultisiteIsFalseWhenFewerThanTwoSitesHaveCanonicalUrls(): void
    {
        $this->assertFalse($this->makeManager(false)->isMultisite());
    }

    public function testIsMultisiteIsTrueWhenTwoOrMoreSitesHaveCanonicalUrls(): void
    {
        $this->assertTrue($this->makeManager(true)->isMultisite());
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeManager(bool $multisite = false): WellKnownFileManager
    {
        $siteService = Mockery::mock(SiteService::class);
        if ($multisite) {
            // Return two sites with canonical URLs so the "> 1" threshold is met.
            $s1 = Mockery::mock(Site::class);
            $s1->shouldReceive('getSiteCanonicalURL')->andReturn('https://site1.example.com');
            $s2 = Mockery::mock(Site::class);
            $s2->shouldReceive('getSiteCanonicalURL')->andReturn('https://site2.example.com');
            $siteService->shouldReceive('getList')->andReturn([$s1, $s2]);
        } else {
            $siteService->shouldReceive('getList')->andReturn([]);
        }

        return new WellKnownFileManager($siteService);
    }

    private function makeSite(string $canonicalUrl): Site
    {
        $site = Mockery::mock(Site::class);
        $site->shouldReceive('getSiteCanonicalURL')->andReturn($canonicalUrl);

        return $site;
    }
}
