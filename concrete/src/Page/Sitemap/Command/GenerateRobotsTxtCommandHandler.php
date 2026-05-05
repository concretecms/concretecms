<?php

declare(strict_types=1);

namespace Concrete\Core\Page\Sitemap\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Core\Site\WellKnown\WellKnownFileManager;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Writes a per-site robots.txt to application/files/site-specific/{host}/robots.txt.
 *
 * Content is derived from the base robots.txt at the web root (if present). Any existing
 * "Sitemap:" directives are stripped and replaced with a single directive pointing to the
 * site's sitemap.xml at its canonical URL. This ensures each site's robots.txt advertises
 * the correct sitemap without cross-site contamination.
 *
 * Sites without a canonical URL are skipped — a valid absolute Sitemap: URL cannot be
 * constructed for them.
 */
class GenerateRobotsTxtCommandHandler implements OutputAwareInterface
{
    use OutputAwareTrait;

    /** @var SiteService */
    protected $siteService;

    /** @var WellKnownFileManager */
    protected $wellKnownManager;

    public function __construct(SiteService $siteService, WellKnownFileManager $wellKnownManager)
    {
        $this->siteService = $siteService;
        $this->wellKnownManager = $wellKnownManager;
    }

    /**
     * Execute the command: write a per-site robots.txt to the well-known directory.
     *
     * @throws \RuntimeException if the site ID is not found
     */
    public function __invoke(GenerateRobotsTxtCommand $command): void
    {
        $site = $this->siteService->getByID($command->getSiteID());
        if ($site === null) {
            throw new \RuntimeException(sprintf('Site ID %d not found.', $command->getSiteID()));
        }

        if ($site->getSiteCanonicalURL() === '') {
            $this->output->write(t('Skipping robots.txt for site "%s": no canonical URL configured.', $site->getSiteHandle()));

            return;
        }

        $baseFile = rtrim(DIR_BASE, '/') . '/robots.txt';
        $baseContent = is_file($baseFile) ? (string) file_get_contents($baseFile) : '';

        $lines = $baseContent !== ''
            ? explode("\n", rtrim($baseContent, "\r\n"))
            : [];

        // Strip any existing Sitemap: directives so we don't duplicate them.
        $lines = array_values(array_filter($lines, static function (string $line): bool { return stripos(trim($line), 'sitemap:') !== 0; }));

        // Append the canonical Sitemap: directive, with a blank separator only when there is base content above it.
        $sitemapUrl = WellKnownFileManager::getUrlForSite($site, 'sitemap.xml');
        if ($lines !== []) {
            $lines[] = '';
        }
        $lines[] = 'Sitemap: ' . $sitemapUrl;

        $content = implode("\n", $lines) . "\n";
        $path = $this->wellKnownManager->writeFile($site, 'robots.txt', $content);

        $this->output->write(t('robots.txt written: %s', $path));
    }
}
