<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group;

use Concrete\Core\Entity\Site\Site;

class SiteGroup implements GroupInterface
{

    protected $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function getEntryGroupLabel()
    {
        return $this->site->getSiteName();
    }

    public function getEntryGroupIdentifier()
    {
        return $this->site->getSiteID();
    }
}
