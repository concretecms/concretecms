<?php

declare(strict_types=1);

namespace Concrete\Core\Page\Sitemap\Command;

use Concrete\Core\Foundation\Command\Command;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Command that triggers generation of the XML sitemap for a single site.
 *
 * Dispatch with an explicit siteID to generate a per-site sitemap file
 * (`sitemap-<handle>.xml`) and copy it into the well-known directory so nginx
 * can serve it at the canonical `/sitemap.xml` path. Dispatching without a
 * siteID is deprecated — it falls back to the active site and writes to the
 * legacy `sitemap.xml` filename for backward compatibility.
 */
class GenerateSitemapCommand extends Command
{
    /** @var int|null */
    private $siteID;

    /**
     * @param int|null $siteID ID of the site to generate for. Omitting (null) is deprecated.
     */
    public function __construct(?int $siteID = null)
    {
        $this->siteID = $siteID;
    }

    /**
     * Return the site ID to generate for, or null when none was specified (deprecated).
     */
    public function getSiteID(): ?int
    {
        return $this->siteID;
    }
}
