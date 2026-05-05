<?php

declare(strict_types=1);

namespace Concrete\Core\Site\WellKnown;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Site\InstallationService;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Manages per-site "well-known" files (robots.txt, sitemap.xml, ads.txt, etc.)
 *
 * Multisite installs store files under
 * {webroot}/application/files/site-specific/{canonical-host}/ so each site
 * can serve its own copies without conflict; nginx/Apache route requests by
 * the Host header to that directory.
 *
 * Single-site installs write files directly to the webroot so no web-server
 * configuration changes are required. security.txt is always stored under
 * .well-known/ per RFC 9116.
 */
class WellKnownFileManager
{
    /** @var bool */
    private $isMultisite;

    public function __construct(InstallationService $installationService)
    {
        $this->isMultisite = $installationService->isMultisiteEnabled();
    }

    /**
     * Return the canonical host for a site, lowercased, usable as a filesystem directory name.
     *
     * Returns '' if the site has no canonical URL, the URL is unparseable, or the host
     * contains characters that could escape the expected storage directory (e.g. '../').
     * Valid hosts contain only letters, digits, hyphens, and dots, and must begin and end
     * with an alphanumeric character — matching RFC 1123 and ruling out path traversal.
     */
    public static function getHostForSite(Site $site): string
    {
        $canonical = $site->getSiteCanonicalURL();
        if ($canonical === '') {
            return '';
        }
        $host = parse_url($canonical, PHP_URL_HOST);
        if ($host === false || $host === null) {
            return '';
        }
        $host = strtolower((string) $host);

        // Reject anything that isn't a plain hostname component: no slashes, no null bytes,
        // no leading/trailing dots or hyphens, no '..' sequences.
        return preg_match('/^[a-z0-9](?:[a-z0-9.\-]*[a-z0-9])?$/', $host) ? $host : '';
    }

    /**
     * Return the absolute filesystem directory where well-known files for a site are stored.
     *
     * Returns '' if the site has no canonical URL.
     */
    public static function getDirectoryForSite(Site $site): string
    {
        $host = static::getHostForSite($site);
        if ($host === '') {
            return '';
        }

        return rtrim(DIR_BASE, '/') . '/application/files/site-specific/' . $host;
    }

    /**
     * Return the per-host filesystem path for a specific well-known file.
     *
     * Returns '' if the site has no canonical URL. This static method always returns
     * the per-host path regardless of multisite mode; use getFilePath() for the
     * mode-aware version.
     */
    public static function getFilePathForSite(Site $site, string $filename): string
    {
        $dir = static::getDirectoryForSite($site);
        if ($dir === '') {
            return '';
        }

        return $dir . '/' . ltrim($filename, '/');
    }

    /**
     * Return the public URL for a well-known file on a site (e.g. https://example.com/robots.txt).
     *
     * Returns '' if the site has no canonical URL.
     */
    public static function getUrlForSite(Site $site, string $filename): string
    {
        $canonical = $site->getSiteCanonicalURL();
        if ($canonical === '') {
            return '';
        }

        return rtrim($canonical, '/') . '/' . ltrim($filename, '/');
    }

    /**
     * Return the filesystem path for a well-known file, respecting multisite mode.
     *
     * In multisite mode returns the per-host path ('' when the site has no canonical URL).
     * In single-site mode always returns the webroot path.
     */
    public function getFilePath(Site $site, string $filename): string
    {
        if ($this->isMultisite) {
            return static::getFilePathForSite($site, $filename);
        }

        return rtrim(DIR_BASE, '/') . '/' . ltrim(static::getWebrootFilename($filename), '/');
    }

    /**
     * Ensure the well-known directory for a site exists, creating it if necessary.
     *
     * Returns the directory path, or '' if the site has no canonical URL.
     */
    public function ensureDirectoryForSite(Site $site): string
    {
        $dir = static::getDirectoryForSite($site);
        if ($dir !== '' && !is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir;
    }

    /**
     * Write a well-known file for a site atomically.
     *
     * In multisite mode the file is written to the per-host directory under
     * application/files/site-specific/; returns '' if the site has no canonical URL.
     * In single-site mode the file is written directly to the webroot so no
     * web-server configuration changes are required.
     *
     * Uses a write-to-tempfile + rename() pattern so the live file is never partially written.
     *
     * Returns the absolute path written, or '' on failure.
     */
    public function writeFile(Site $site, string $filename, string $content): string
    {
        if ($this->isMultisite) {
            $dir = $this->ensureDirectoryForSite($site);
            if ($dir === '') {
                return '';
            }

            return $this->writeToPath($dir . '/' . ltrim($filename, '/'), $content);
        }

        // Single-site: always write to the webroot
        $path = rtrim(DIR_BASE, '/') . '/' . ltrim(static::getWebrootFilename($filename), '/');

        return $this->writeToPath($path, $content);
    }

    /**
     * Map a storage filename to its webroot path.
     *
     * security.txt must be served from /.well-known/security.txt per RFC 9116.
     * In per-host (multisite) storage the web server handles the URL mapping, so
     * only the webroot case needs the subdirectory.
     */
    private static function getWebrootFilename(string $filename): string
    {
        return $filename === 'security.txt' ? '.well-known/security.txt' : $filename;
    }

    /**
     * Atomically write $content to $path, creating parent directories as needed.
     *
     * Returns the path on success or '' on failure.
     */
    private function writeToPath(string $path, string $content): string
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $tmp = tempnam($dir, '.tmp');
        if ($tmp === false) {
            return '';
        }
        if (file_put_contents($tmp, $content, LOCK_EX) === false) {
            @unlink($tmp);
            return '';
        }
        if (!rename($tmp, $path)) {
            @unlink($tmp);
            return '';
        }

        @chmod($path, 0644);

        return $path;
    }
}
