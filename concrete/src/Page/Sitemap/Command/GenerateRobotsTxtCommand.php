<?php

declare(strict_types=1);

namespace Concrete\Core\Page\Sitemap\Command;

use Concrete\Core\Foundation\Command\Command;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Command that triggers generation of a per-site robots.txt file.
 *
 * The handler derives the robots.txt content from the base file at the web root,
 * strips any existing Sitemap: directives, and appends one pointing to the site's
 * canonical sitemap URL. The result is written to
 * `application/files/site-specific/{host}/robots.txt` so nginx/Apache can serve
 * it at `/robots.txt` per-domain.
 */
class GenerateRobotsTxtCommand extends Command
{
    /** @var int */
    private $siteID;

    public function __construct(int $siteID)
    {
        $this->siteID = $siteID;
    }

    /**
     * Return the ID of the site this command targets.
     */
    public function getSiteID(): int
    {
        return $this->siteID;
    }
}
