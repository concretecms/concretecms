<?php

declare(strict_types=1);

namespace Concrete\Core\Page\Sitemap\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Page\Sitemap\Element\SitemapElement;
use Concrete\Core\Page\Sitemap\Element\SitemapPage;
use Concrete\Core\Page\Sitemap\SitemapWriter;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Core\Site\WellKnown\WellKnownFileManager;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Handles GenerateSitemapCommand: writes the XML sitemap for a single site.
 *
 * Uses WellKnownFileManager::getFilePath() so the output path is mode-aware:
 * multisite installs write to application/files/site-specific/{host}/sitemap.xml;
 * single-site installs write directly to {webroot}/sitemap.xml.
 */
class GenerateSitemapCommandHandler implements OutputAwareInterface
{
    use OutputAwareTrait;

    /** @var SitemapWriter */
    protected $writer;

    /** @var SiteService */
    protected $siteService;

    /** @var WellKnownFileManager */
    protected $wellKnownManager;

    public function __construct(SitemapWriter $writer, SiteService $siteService, WellKnownFileManager $wellKnownManager)
    {
        $this->writer = $writer;
        $this->siteService = $siteService;
        $this->wellKnownManager = $wellKnownManager;
    }

    /**
     * Execute the sitemap generation command.
     *
     * When the command carries an explicit siteID, ensures the per-host well-known directory
     * exists, sets the output path to `application/files/site-specific/<host>/sitemap.xml`,
     * then delegates to SitemapWriter. Writing directly to the well-known path means there is
     * only ever one copy of the file on disk and no separate copy step can fail silently.
     *
     * When siteID is null (deprecated), falls back to the active site and writes to
     * the legacy `sitemap.xml` filename for backward compatibility.
     *
     * @throws \RuntimeException if the specified site ID is not found, or no active site can be resolved
     */
    public function __invoke(GenerateSitemapCommand $command): void
    {
        $numPages = 0;
        $pulse = static function (SitemapElement $element) use (&$numPages): void {
            if ($element instanceof SitemapPage) {
                $numPages++;
            }
        };

        $siteID = $command->getSiteID();
        if ($siteID !== null) {
            $site = $this->siteService->getByID($siteID);
            if ($site === null) {
                throw new \RuntimeException(sprintf('Site ID %d not found.', $siteID));
            }

            // Route the output to the correct location for this install mode:
            // multisite → per-host directory, single-site → webroot.
            $outputPath = $this->wellKnownManager->getFilePath($site, 'sitemap.xml');
            if ($outputPath !== '') {
                $dir = dirname($outputPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $this->writer->setOutputFilename($outputPath);
            }

            $this->writer->generateForSite($site, '', $pulse);

            $url = WellKnownFileManager::getUrlForSite($site, 'sitemap.xml');
        } else {
            // Null siteID is deprecated — pass an explicit siteID to GenerateSitemapCommand.
            // Preserved for backward compatibility. Uses the legacy sitemap.xml filename.
            $site = $this->siteService->getSite();
            if ($site === null) {
                throw new \RuntimeException('No active site found. Pass an explicit siteID to GenerateSitemapCommand.');
            }
            // Pre-set the resolved legacy filename so generateForSite() writes there instead of
            // sitemap-<handle>.xml, and the finally block restores it correctly.
            $legacyFilename = $this->writer->getOutputFilename();
            $this->writer->setOutputFilename($legacyFilename);
            $this->writer->generateForSite($site, '', $pulse);
            // generateForSite() restores generator state, so compute the URL directly.
            $canonicalUrl = $site->getSiteCanonicalURL();
            $url = $canonicalUrl !== '' && strpos($legacyFilename, DIR_BASE . '/') === 0
                ? rtrim($canonicalUrl, '/') . substr($legacyFilename, strlen(DIR_BASE))
                : '';
        }

        if ($url !== '') {
            $this->output->write(t('Sitemap written: %s (%d pages)', $url, $numPages));
        } else {
            $this->output->write(t('Sitemap written (%d pages)', $numPages));
        }
    }
}
