<?php
namespace Concrete\Core\Site;

use Concrete\Core\Entity\Site\Site;
use Symfony\Component\EventDispatcher\Event;

class SiteEvent extends Event
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
