<?php
namespace Concrete\Core\Localization\Locale;

use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Localization\Service\CountryList;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Doctrine\ORM\EntityManagerInterface;

class LocaleCollection implements \JsonSerializable
{

    protected $flag;
    protected $tree;

    public function __construct(SiteTree $tree)
    {
        $this->flag = new Flag();
        $this->countries = new CountryList();
        $this->tree = $tree;
    }

    protected function getLocaleDisplayName(Locale $locale)
    {
        $name = $locale->getLanguageText();
        foreach($this->getLocales() as $otherLocale) {
            if ($otherLocale->getSiteLocaleID() != $locale->getSiteLocaleID() && $otherLocale->getLanguage () == $locale->getLanguage()) {
                $name = sprintf('%s (%s)', $locale->getLanguageText(), $this->countries->getCountryName($locale->getCountry()));
            }
        }
        return $name;
    }

    public function jsonSerialize()
    {
        $locales = array();
        foreach($this->getLocales() as $locale) {
            $locales[] = [
                'id' => $locale->getSiteLocaleID(),
                'locale' => $locale->getLocale(),
                'localeDisplayName' => $this->getLocaleDisplayName($locale),
                'icon' => (string) $this->flag->getLocaleFlagIcon($locale, true),
                'treeID' => $locale->getSiteTree()->getSiteTreeID(),
                'selectedLocale' => $locale->getSiteLocaleID() == $this->tree->getLocale()->getSiteLocaleID(),
            ];
        }
        return $locales;
    }

    public function getLocales()
    {
        return $this->tree->getSite()->getLocales();
    }

}
