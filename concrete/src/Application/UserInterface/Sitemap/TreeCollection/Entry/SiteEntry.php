<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry;

use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use HtmlObject\Element;
use HtmlObject\Image;

/**
 * @since 8.2.0
 */
class SiteEntry extends Entry
{

    protected $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function getLabel()
    {
        return $this->site->getSiteName();
    }

    public function getSiteTreeID()
    {
        return $this->site->getSiteTreeID();
    }

    public function getIconElement()
    {
        return null;
    }

    /**
     * @since 8.4.0
     */
    public function getIcon()
    {
        return null;
    }

    public function getID()
    {
        return $this->site->getSiteID();
    }

    public function getGroupClass()
    {
        return null;
    }

}
