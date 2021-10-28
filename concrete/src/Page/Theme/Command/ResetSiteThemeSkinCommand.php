<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Foundation\Command\Command;

class ResetSiteThemeSkinCommand extends Command
{

    /**
     * @var Site
     */
    protected $site;

    /**
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * @return Site
     */
    public function getSite(): Site
    {
        return $this->site;
    }

    /**
     * @param Site $site
     */
    public function setSite(Site $site): void
    {
        $this->site = $site;
    }


}
