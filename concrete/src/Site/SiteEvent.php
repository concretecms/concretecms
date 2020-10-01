<?php
namespace Concrete\Core\Site;

use Concrete\Core\Entity\Site\Site;

class SiteEvent
{

    protected $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }



}
