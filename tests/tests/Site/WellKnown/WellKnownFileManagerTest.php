<?php

namespace Concrete\Tests\Site\WellKnown;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Site\WellKnown\WellKnownFileManager;
use Concrete\Tests\TestCase;
use Mockery;

class WellKnownFileManagerTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Static helper methods
    // -------------------------------------------------------------------------

    public function testGetHostForSiteReturnsLowercasedHost(): void
    {
        $site = $this->makeSite('https://Example.COM/');
        $this->assertSame('example.com', WellKnownFileManager::getHostForSite($site));
    }

    public function testGetHostForSiteReturnsEmptyStringWhenNoCanonicalUrl(): void
    {
        $site = $this->makeSite('');
        $this->assertSame('', WellKnownFileManager::getHostForSite($site));
    }

    public function testGetHostForSiteStripsPort(): void
    {
        $site = $this->makeSite('https://example.com:8443/');
        // parse_url returns host without port
        $this->assertSame('example.com', WellKnownFileManager::getHostForSite($site));
    }

    public function testGetDirectoryForSiteReturnsPathUnderSiteFiles(): void
    {
        $site = $this->makeSite('https://example.com');
        $dir = WellKnownFileManager::getDirectoryForSite($site);
        $this->assertStringEndsWith('/site-files/example.com', $dir);
        $this->assertStringStartsWith(rtrim(DIR_BASE, '/'), $dir);
    }

    public function testGetDirectoryForSiteReturnsEmptyStringWhenNoCanonicalUrl(): void
    {
        $site = $this->makeSite('');
        $this->assertSame('', WellKnownFileManager::getDirectoryForSite($site));
    }

    public function testGetFilePathForSiteComposesCorrectPath(): void
    {
        $site = $this->makeSite('https://example.com');
        $path = WellKnownFileManager::getFilePathForSite($site, 'robots.txt');
        $this->assertStringEndsWith('/site-files/example.com/robots.txt', $path);
    }

    public function testGetFilePathForSiteStripsLeadingSlashFromFilename(): void
    {
        $site = $this->makeSite('https://example.com');
        $withSlash    = WellKnownFileManager::getFilePathForSite($site, '/robots.txt');
        $withoutSlash = WellKnownFileManager::getFilePathForSite($site, 'robots.txt');
        $this->assertSame($withoutSlash, $withSlash);
    }

    public function testGetFilePathForSiteReturnsEmptyStringWhenNoCanonicalUrl(): void
    {
        $site = $this->makeSite('');
        $this->assertSame('', WellKnownFileManager::getFilePathForSite($site, 'robots.txt'));
    }

    public function testGetUrlForSiteComposesCorrectUrl(): void
    {
        $site = $this->makeSite('https://example.com');
        $this->assertSame('https://example.com/sitemap.xml', WellKnownFileManager::getUrlForSite($site, 'sitemap.xml'));
    }

    public function testGetUrlForSiteStripsTrailingSlashFromCanonical(): void
    {
        $site = $this->makeSite('https://example.com/');
        $this->assertSame('https://example.com/robots.txt', WellKnownFileManager::getUrlForSite($site, 'robots.txt'));
    }

    public function testGetUrlForSiteReturnsEmptyStringWhenNoCanonicalUrl(): void
    {
        $site = $this->makeSite('');
        $this->assertSame('', WellKnownFileManager::getUrlForSite($site, 'robots.txt'));
    }

    // -------------------------------------------------------------------------
    // writeFile()
    // -------------------------------------------------------------------------

    public function testWriteFileCreatesDirectoryAndFile(): void
    {
        $site = $this->makeSite('https://writetest.example.com');
        $manager = new WellKnownFileManager();

        $path = $manager->writeFile($site, 'robots.txt', "User-agent: *\nDisallow:\n");

        $this->assertFileExists($path);
        $this->assertStringContainsString('User-agent', (string) file_get_contents($path));

        // Clean up
        @unlink($path);
        @rmdir(WellKnownFileManager::getDirectoryForSite($site));
    }

    public function testWriteFileReturnsEmptyStringWhenNoCanonicalUrl(): void
    {
        $site = $this->makeSite('');
        $manager = new WellKnownFileManager();
        $this->assertSame('', $manager->writeFile($site, 'robots.txt', 'content'));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeSite(string $canonicalUrl): Site
    {
        $site = Mockery::mock(Site::class);
        $site->shouldReceive('getSiteCanonicalURL')->andReturn($canonicalUrl);

        return $site;
    }
}
