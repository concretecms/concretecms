<?php

declare(strict_types=1);

namespace Concrete\Core\Site\WellKnown;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Site\Service as SiteService;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Manages per-site "well-known" files (robots.txt, sitemap.xml, ads.txt, etc.).
 *
 * When two or more sites have canonical URLs configured, files are stored under
 * {webroot}/application/files/site-specific/{canonical-host}/ and nginx/Apache
 * route requests by Host header to the matching directory.
 *
 * When only one site is configured (even if the multisite feature flag is on),
 * files are written directly to the webroot so no web-server reconfiguration is
 * required. security.txt is always served from /.well-known/security.txt per RFC 9116.
 *
 * Path resolution and file writing are intentionally kept in one class rather than
 * split into a separate Writer. The write path is derived from the same isMultisite
 * flag, host validation, and getWebrootFilename() mapping that the read/query methods
 * use. A Writer would need to duplicate or depend on all of that anyway, producing
 * two coupled classes with no reduction in complexity. If this class is replaced via
 * the container, both concerns are replaced together, which is the right behaviour.
 */
class WellKnownFileManager
{
    /**
     * @var bool
     */
    private $isMultisite;

    /**
     * @var string[]
     */
    private const ALLOWED_FILENAMES = ['robots.txt', 'sitemap.xml', 'llms.txt', 'security.txt', 'ads.txt', 'humans.txt'];

    /**
     * @param SiteService $siteService Used to count how many sites have canonical URLs at construction time
     */
    public function __construct(SiteService $siteService)
    {
        // Use per-host routing only when two or more sites have canonical URLs.
        // "Multisite enabled" alone is not the right threshold: a system with the
        // multisite feature on but only one site configured should behave exactly
        // like a single-site install — files land in the webroot and no web-server
        // reconfiguration is needed. Every site is counted, not just those visible to
        // the current user: file routing is an infrastructure decision that must
        // reflect the actual system topology, not what a particular admin can see.
        $count = 0;
        foreach ($siteService->getList() as $site) {
            if ($site->getSiteCanonicalURL() !== '') {
                $count++;
            }
        }
        $this->isMultisite = $count > 1;
    }

    /**
     * Whether well-known files are stored per-host rather than in the webroot.
     *
     * True when two or more sites have canonical URLs, in which case the web server
     * must route requests to the per-host directory. The dashboard uses this to decide
     * whether to show the nginx/Apache routing snippet, so what is displayed always
     * matches where files are actually written.
     */
    public function isMultisite(): bool
    {
        return $this->isMultisite;
    }

    /**
     * Return the canonical host for a site, lowercased, usable as a filesystem directory name.
     *
     * Returns '' if the site has no canonical URL, the URL is unparseable, or the host
     * contains characters that could escape the expected storage directory (e.g. '../').
     * Valid hosts contain only letters, digits, hyphens, and dots, and must begin and end
     * with an alphanumeric character — matching RFC 1123 and ruling out path traversal.
     *
     * @param Site $site The site whose canonical URL is examined
     *
     * @return string Lowercased hostname, or '' if not resolvable or contains unsafe characters
     */
    public function getHostForSite(Site $site): string
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
     *
     * @param Site $site The site whose well-known directory is requested
     *
     * @return string Absolute directory path, or '' if the site has no canonical URL
     */
    public function getDirectoryForSite(Site $site): string
    {
        $host = $this->getHostForSite($site);
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
     *
     * @param Site $site The site whose per-host path is requested
     * @param string $filename One of the allowed well-known filenames
     *
     * @return string Absolute file path, or '' if the site has no canonical URL or filename is disallowed
     */
    public function getFilePathForSite(Site $site, string $filename): string
    {
        if (!in_array($filename, self::ALLOWED_FILENAMES, true)) {
            return '';
        }

        $dir = $this->getDirectoryForSite($site);
        if ($dir === '') {
            return '';
        }

        return $dir . '/' . $filename;
    }

    /**
     * Return the public URL for a well-known file on a site (e.g. https://example.com/robots.txt).
     *
     * Returns '' if the site has no canonical URL or the filename is not allowed.
     * security.txt is mapped to /.well-known/security.txt per RFC 9116 automatically.
     *
     * @param Site $site The site whose canonical URL is used as the base
     * @param string $filename One of the allowed well-known filenames
     *
     * @return string Full public URL, or '' if the site has no canonical URL or filename is disallowed
     */
    public function getUrlForSite(Site $site, string $filename): string
    {
        if (!in_array($filename, self::ALLOWED_FILENAMES, true)) {
            return '';
        }

        $canonical = $site->getSiteCanonicalURL();
        if ($canonical === '') {
            return '';
        }

        return rtrim($canonical, '/') . '/' . ltrim($this->getWebrootFilename($filename), '/');
    }

    /**
     * Return the filesystem path for a well-known file, respecting multisite mode.
     *
     * In multisite mode returns the per-host path ('' when the site has no canonical URL).
     * In single-site mode always returns the webroot path.
     *
     * @param Site $site The site whose file path is requested
     * @param string $filename One of the allowed well-known filenames
     *
     * @return string Absolute filesystem path, or '' in multisite mode when the site has no canonical URL
     */
    public function getFilePath(Site $site, string $filename): string
    {
        if (!in_array($filename, self::ALLOWED_FILENAMES, true)) {
            return '';
        }

        if ($this->isMultisite) {
            return $this->getFilePathForSite($site, $filename);
        }

        return rtrim(DIR_BASE, '/') . '/' . ltrim($this->getWebrootFilename($filename), '/');
    }

    /**
     * Ensure the well-known directory for a site exists, creating it if necessary.
     *
     * Returns the directory path, or '' if the site has no canonical URL.
     *
     * @param Site $site The site whose well-known directory should be created if absent
     *
     * @return string The directory path, or '' if the site has no canonical URL
     */
    public function ensureDirectoryForSite(Site $site): string
    {
        $dir = $this->getDirectoryForSite($site);
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
     * @param Site $site The site the file belongs to
     * @param string $filename One of the allowed well-known filenames
     * @param string $content File content to write
     *
     * @return string The absolute path written, or '' on failure
     */
    public function writeFile(Site $site, string $filename, string $content): string
    {
        if (!in_array($filename, self::ALLOWED_FILENAMES, true)) {
            return '';
        }

        if ($this->isMultisite) {
            $dir = $this->ensureDirectoryForSite($site);
            if ($dir === '') {
                return '';
            }

            return $this->writeToPath($dir . '/' . ltrim($filename, '/'), $content);
        }

        // Single-site: always write to the webroot
        $path = rtrim(DIR_BASE, '/') . '/' . ltrim($this->getWebrootFilename($filename), '/');

        return $this->writeToPath($path, $content);
    }

    /**
     * Map a storage filename to its webroot path.
     *
     * security.txt must be served from /.well-known/security.txt per RFC 9116.
     * In per-host (multisite) storage the web server handles the URL mapping, so
     * only the webroot case needs the subdirectory.
     */
    private function getWebrootFilename(string $filename): string
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
        $resolvedDir = realpath($dir);
        if ($resolvedDir === false) {
            return '';
        }
        $path = $resolvedDir . DIRECTORY_SEPARATOR . basename($path);
        $tmp = tempnam($resolvedDir, '.tmp');
        if ($tmp === false) {
            return '';
        }
        if (file_put_contents($tmp, $content, LOCK_EX) === false) {
            unlink($tmp);

            return '';
        }
        if (!rename($tmp, $path)) {
            unlink($tmp);

            return '';
        }

        chmod($path, 0644);

        return $path;
    }
}
