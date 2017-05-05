<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry;

use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use HtmlObject\Element;
use HtmlObject\Image;

class LocaleEntry extends Entry
{

    protected $locale;

    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
    }

    public function getLabel()
    {
        return $this->locale->getLanguageText();
    }

    public function getSiteTreeID()
    {
        return $this->locale->getSiteTree()->getSiteTreeID();
    }

    public function getIconElement()
    {
        return Flag::getLocaleFlagIcon($this->locale);
    }

    public function getID()
    {
        return $this->locale->getLocaleID();
    }

    public function getGroupClass()
    {
        $site = $this->locale->getSite();
        if ($site) {
            return $site->getSiteID();
        }
    }

}
